<?php

namespace Modules\BookStore\App\Http\Services;

use Modules\BookStore\App\Http\Repositories\BookStoreRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\FileUploader;

class BookStoreService
{
    protected $repository;

    public function __construct(BookStoreRepository $repository)
    {
        $this->repository = $repository;
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
        $fetchData = $this->mapCommonFields($data);
        $fetchData['created_at'] = date('Y-m-d H:i:s');
        $fetchData['updated_at'] = date('Y-m-d H:i:s');

        if (isset($data->thumbnail)) {
            $fetchData['thumbnail'] = "uploads/books-thumbnail/" . nice_file_name($data->title, $data->thumbnail->extension());
            FileUploader::upload($data->thumbnail, $fetchData['thumbnail'], 500, null, 200, 200);
        }

        $this->applyBookSource($data, $fetchData);

        return $this->repository->create($fetchData);
    }

    public function update($data, $id)
    {
        try {
            $book = $this->repository->find($id);
            $fetchData = $this->mapCommonFields($data);
            $fetchData['updated_at'] = date('Y-m-d H:i:s');

            if (isset($data->thumbnail)) {
                $fetchData['thumbnail'] = "uploads/books-thumbnail/" . nice_file_name($data->title, $data->thumbnail->extension());
                FileUploader::upload($data->thumbnail, $fetchData['thumbnail'], 500, null, 200, 200);
            }

            $this->applyBookSource($data, $fetchData, $book);

            return $this->repository->update($fetchData, $id);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $book = $this->repository->find($id);
            if ($book && !empty($book->file_path)) {
                remove_file($book->file_path);
            }
            return $this->repository->destroy($id);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    protected function mapCommonFields($data): array
    {
        $fetchData = [];
        $fetchData['added_by']    = Auth::id();
        $fetchData['category_id'] = $data->category_id;
        $fetchData['title']       = $data->title;
        $fetchData['disc']        = $data->description;
        $fetchData['price']       = $data->price;
        $fetchData['slug']        = slugify($data->title);

        if (isset($data->keywords)) {
            $fetchData['keywords'] = $data->keywords;
        }

        if ($data->has('if_discount') && (int) $data->if_discount === 1) {
            $fetchData['if_discount']    = 1;
            $fetchData['discount_price'] = $data->discount_price ?? 0;
        } else {
            $fetchData['if_discount']    = 0;
            $fetchData['discount_price'] = 0;
        }

        return $fetchData;
    }

    /**
     * Apply uploaded PDF or external link onto the payload.
     */
    protected function applyBookSource($data, array &$fetchData, $existingBook = null): void
    {
        $fileType = $data->file_type;
        $fetchData['file_type'] = $fileType;

        if ($fileType === 'file') {
            $fetchData['file_url'] = null;

            if (isset($data->book_file)) {
                $path = public_path('uploads/books-files');
                if (!File::isDirectory($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }

                $fileName = nice_file_name($data->title, $data->book_file->extension());
                $relativePath = 'uploads/books-files/' . $fileName;
                FileUploader::upload($data->book_file, $relativePath);
                $fetchData['file_path'] = $relativePath;

                if ($existingBook && !empty($existingBook->file_path)) {
                    remove_file($existingBook->file_path);
                }
            } elseif ($existingBook) {
                // Keep previous file when editing without a new upload
                $fetchData['file_path'] = $existingBook->file_path;
            }
        } else {
            $fetchData['file_url']  = $data->file_url;
            $fetchData['file_path'] = null;

            if ($existingBook && !empty($existingBook->file_path) && $existingBook->file_type === 'file') {
                remove_file($existingBook->file_path);
            }
        }
    }
}
