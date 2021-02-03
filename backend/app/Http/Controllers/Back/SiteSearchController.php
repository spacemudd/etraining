<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

class SiteSearchController extends Controller
{
    /**
     * 10 characters: to show 10 neighbouring characters around the searched word
     * @var int
     */
    const BUFFER = 10;

    /**
     * Helper function to generate the model namespace
     * @return string
     */
    private function modelNamespacePrefix()
    {
        return app()->getNamespace() . 'Models\Back\\';
    }

    /**
     * Search the site globally.
     *
     * @param $search_string
     * @return string
     */
    public function search($search_string)
    {
        $toExclude = [
            // App\Models\Back\Company::class
        ];

        // Getting all the model files from the model folder
        $files = File::allFiles(app()->basePath() . '/app/Models/Back');

        // To get all the model classes
        $results = collect($files)->map(function (SplFileInfo $file){
            $filename = $file->getRelativePathname();

            // assume model name is equal to file name
            /* making sure it is a php file*/
            if (substr($filename, -4) !== '.php'){
                return null;
            }
            // removing .php
            return substr($filename, 0, -4);
        });

        $results = $results->filter(function (?string $classname) use($toExclude) {
            if($classname === null) {
                return false;
            }

            // using reflection class to obtain class info dynamically
            $reflection = new ReflectionClass($this->modelNamespacePrefix() . $classname);

            // making sure the class extended eloquent model
            $isModel = $reflection->isSubclassOf(Model::class);

            // making sure the model implemented the searchable trait
            $searchable = $reflection->hasMethod('search');

            // filter model that has the searchable trait and not in exclude array
            return $isModel && $searchable && !in_array($reflection->getName(), $toExclude, true);
        });

        $results = $results->map(function ($classname) use ($search_string) {
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
            $fields = array_filter($model::SEARCHABLE_FIELDS, fn($field) => $field !== 'id');
            return $model::search($search_string)->withTrashed()->get()->map(function ($modelRecord) use ($model, $fields, $search_string, $classname) {
                // only extracting the relevant fields from our model
                $fieldsData = $modelRecord->only($fields);

                // setting the model name
                $modelRecord->setAttribute('model', $classname);

                if($classname == "Instructor") {
                    $modelRecord->makeHidden([
                        'reference_number', 'team_id', 'created_at', 'updated_at', 'twitter_link', 'city_id', 'deleted_at',
                        'provided_courses', 'is_approved','approved_by', 'approved_at', 'is_pending_approval', 'approved_by_id', 'status',
                        'cv_full_copy_url', 'cv_summary_copy_url', 'is_pending_uploading_files', 'id', 'user_id'
                        ]);
                } else if($classname == "Trainee") {
                    $modelRecord->makeHidden([
                        'reference_number', 'team_id', 'created_at', 'updated_at', 'name_selectable', 'city_id', 'deleted_at',
                        'provided_courses', 'instructor_id', 'is_pending_approval', 'approved_by_id', 'identity_copy_url',
                        'cv_full_copy_url', 'cv_summary_copy_url', 'is_pending_uploading_files', 'birthday', 'qualification_copy_url', 'id', 'user_id',
                        'educational_level_id','qualification_copy_url', 'bank_account_copy_url', 'marital_status_id', 'children_count', 'company_id'
                        ]);
                } else if($classname == "Course") {
                    $modelRecord->makeHidden([
                        'id', 'instructor_id', 'team_id', 'approval_code', 'twitter_link', 'sharable', 'deleted_at', 'created_at', 'updated_at',
                        'provided_courses', 'is_approved','approved_by', 'approved_at', 'is_pending_approval', 'approved_by_id', 'status', 'training_package_url',
                        'cv_full_copy_url', 'cv_summary_copy_url', 'is_pending_uploading_files', 'classroom_count'
                        ]);
                } else {
                    $modelRecord->makeHidden([
                        'media','has_attachments',  'deleted_at', 'created_at', 'updated_at', 'id', 'team_id', 'company_id', 'created_by_id', 'trainee_salary', 'trainees_count'
                    ]);
                }

                // setting the resource link
                return $modelRecord;
            });
        })->flatten(1);

        // using a standardised site search resource
        return $results->toJSON(JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

        // return SiteSearchResource::collection($results);
    }
}
