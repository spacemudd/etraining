<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Resources\SiteSearchResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class SiteSearchController extends Controller
{

    const BUFFER = 10;

    // 10 characters: to show 10 neighbouring characters around the searched word

    /** A helper function to generate the model namespace
     * @return string
     */
    private function modelNamespacePrefix()
    {
        return app()->getNamespace() . 'Models\Back\\';
    }

    public function search($search_string)
    {

        // If Later We Decided That We No Longer Need Any Model We Can Put It Here.
        $toExclude = [];

        // getting all the model files from the model folder
        $files = File::allFiles(app()->basePath() . '/app/Models/Back');

        // to get all the model classes
        $results = collect($files)->map(function (SplFileInfo $file){
            $filename = $file->getRelativePathname();

            // assume model name is equal to file name
            /* making sure it is a php file*/
            if (substr($filename, -4) !== '.php'){
                return null;
            }
            // removing .php
            return substr($filename, 0, -4);

        })->filter(function (?string $classname) use($toExclude){
            if($classname === null){
                return false;
            }

            // using reflection class to obtain class info dynamically
            $reflection = new \ReflectionClass($this->modelNamespacePrefix() . $classname);

            // making sure the class extended eloquent model
            $isModel = $reflection->isSubclassOf(Model::class);

            // making sure the model implemented the searchable trait
            $searchable = $reflection->hasMethod('search');

            // filter model that has the searchable trait and not in exclude array
            return $isModel && $searchable && !in_array($reflection->getName(), $toExclude, true);

        })->map(function ($classname) use ($search_string) {
            // for each class, call the search function
            $model = app($this->modelNamespacePrefix() . $classname);

            // Our goal here: to add these 3 attributes to each of our search result:
            // a. `match` -- the match found in our model records
            // b. `model` -- the related model name
            // c. `view_link` -- the URL for the user to navigate in the frontend to view the resource

            // to create the `match` attribute, we need to join the value of all the searchable fields in
            // our model, ie all the fields defined in our 'toSearchableArray' model method

            // We make use of the SEARCHABLE_FIELDS constant in our model
            // We dont want id in the match, so we filter it out.

            // NEEDS EDITING DEPENDS ON THE RESULTS
            $fields = array_filter($model::SEARCHABLE_FIELDS);
            return $model::search($search_string)->get()->map(function ($modelRecord) use ($model, $fields, $search_string, $classname){

                // only extracting the relevant fields from our model
                $fieldsData = $modelRecord->only($fields);

                // setting the model name
                $modelRecord->setAttribute('model', $classname);
                // setting the resource link
                return $modelRecord;

            });
        })->flatten(1);

        // using a standardised site search resource
        return SiteSearchResource::collection($results);

    }

}
