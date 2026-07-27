<?php

namespace Modules\BookStore\App\Http\Services;
use Modules\BookStore\App\Http\Repositories\BookStoreRepository;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\FileUploader;

class BookStoreService
{
    /**
     * Create a new class instance.
     */
    protected $repository;

    public function __construct(BookStoreRepository $repository)
    {
        return $this->repository = $repository;
    }

    public function get($search)
    {
        return $this->repository->get($search);
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function activation($id)
    {
        return $this->repository->activation($id);
    }




    public function create($data)
    {
       // dd($data);
        try {
            $fetchData['added_by']     = Auth::id();
            $fetchData['category_id']  = $data->category_id;
            $fetchData['title']        = $data->title;
            $fetchData['if_discount']  = $data->if_discount ?? 0;
            if($data->if_discount==1){
                $fetchData['if_discount']      = 1;
                $fetchData['discount_price']   = $data->discount_price ?? 0;
            }else{
                $fetchData['if_discount']      =  0;
                $fetchData['discount_price']   = 0;
            }

            $fetchData['disc']         = $data->description;
            $fetchData['price']        = $data->price;
            $fetchData['slug']         = slugify($data->title);
            $fetchData['created_at']   = date('Y-m-d H:i:s');
            $fetchData['updated_at']   = date('Y-m-d H:i:s');

            if(isset($data->thumbnail)){
                $fetchData['thumbnail'] = "uploads/books-thumbnail/" . nice_file_name($data->title, $data->thumbnail->extension());
                FileUploader::upload($data->thumbnail, $fetchData['thumbnail'], 500, null, 200, 200);
            }

            $data = $this->repository->create($fetchData);
            return $data;
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function update($data,$id)
    {
        try {
            $fetchData['added_by']     = Auth::id();
            $fetchData['category_id']  = $data->category_id;
            $fetchData['title']        = $data->title;
            $fetchData['keywords']     = $data->keywords;
            $fetchData['disc']         = $data->description;
            $fetchData['price']        = $data->price;
            $fetchData['slug']         = slugify($data->title);
            $fetchData['created_at']   = date('Y-m-d H:i:s');
            $fetchData['updated_at']   = date('Y-m-d H:i:s');
           if($data->if_discount==1){
                $fetchData['if_discount']      = 1;
                $fetchData['discount_price']   = $data->discount_price ?? 0;
            }else{
                $fetchData['if_discount']      = 0;
                $fetchData['discount_price']   = 0;
            }
            if(isset($data->thumbnail)){
                $fetchData['thumbnail'] = "uploads/books-thumbnail/" . nice_file_name($data->title, $data->thumbnail->extension());
                FileUploader::upload($data->thumbnail, $fetchData['thumbnail'], 500, null, 200, 200);
            }

            $data = $this->repository->update($fetchData,$id);
            return $data;
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id){
        try{
            return $this->repository->destroy($id);
         } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }

    }

}
