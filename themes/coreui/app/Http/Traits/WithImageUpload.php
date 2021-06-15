<?php


namespace App\Http\Traits;


use Illuminate\Support\Facades\Request;

trait WithImageUpload
{

    public function uploadImage(Request $request, $fieldName) {
        if( !$request->hasFile( $fieldName ) ) {
            return;
        }
        $imageName = time().'.'.$request->file($fieldName)->extension();

        $request->file($fieldName)->move(public_path('images'), $imageName);

        $this->update([
            $fieldName => $imageName
        ]);
    }

}
