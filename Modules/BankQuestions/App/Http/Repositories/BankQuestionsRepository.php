<?php

namespace Modules\BookStore\App\Http\Repositories;
use Modules\BookStore\App\Models\Book;
class BankQuestionsRepository
{
    /**
     * Create a new class instance.
     */
    public $model;

    public function __construct(Book $model)
    {
        $this->model = $model;
    }
    
    public function get($search = null){

        return $this->model->get();
    }

    public function all()
    {
        return $this->model->all();
    }


    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function activation($id)
    {
        $model = $this->model->findOrFail($id);
        $model->status == '1' ? $model->status = 0 : $model->status = 1;
        $model->save();
        return $model;
    }


    

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update( array $data ,$id)
    {
        $model = $this->model->findOrFail($id);
        $model->update($data);
        return $model;
    }

    public function destroy($id)
    {
        $model = $this->model->findOrFail($id);
        $model->delete();
        return true;
    }
}
