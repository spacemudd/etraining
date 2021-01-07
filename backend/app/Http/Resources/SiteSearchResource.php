<?php
namespace App\Http\Resources;


/* This Resource IS NOT IN USE:

I HAVE CREATED IT AS A BACKUP FOR US IF THE RESULTS WERE TOO COMPLICATED AND THAT WE COULDN"T HANDLE IT

*/

use Illuminate\Http\Resources\Json\JsonResource;

class SiteSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
       return $this->setEncodingOptions(JSON_UNESCAPED_SLASHES);;
    }

}
