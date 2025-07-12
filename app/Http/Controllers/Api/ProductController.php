<?php
namespace App\Http\Controllers\Api;

use App\Enums\ResponseTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Services\HelperService;
use App\Http\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private ProductService $service, private HelperService $helper)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $queries = request()->input();
        $res     = $this->service->getDocs($queries);
        if (! $res['ok'] && $res['type'] === ResponseTypes::SERVER_ERROR) {
            return $this->helper->serverException($res['data']);
        }
        if (! $res['ok'] && $res['type'] === ResponseTypes::ERROR) {
            return $this->helper->messageException($res['message'], $res['code']);
        }
        return $this->helper->entity($res['data']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $body = $request->validated();
        $res  = $this->service->createDoc($body);
        if (! $res['ok'] && $res['type'] === ResponseTypes::SERVER_ERROR) {
            return $this->helper->serverException($res['data']);
        }
        if (! $res['ok'] && $res['type'] === ResponseTypes::ERROR) {
            return $this->helper->messageException($res['message'], $res['code']);
        }
        return $this->helper->entity($res['data'], $res['code'], $res['type'], $res['message']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $queries = request()->input();
        $res     = $this->service->getDoc($id, $queries);
        if (! $res['ok'] && $res['type'] === ResponseTypes::SERVER_ERROR) {
            return $this->helper->serverException($res['data']);
        }
        if (! $res['ok'] && $res['type'] === ResponseTypes::ERROR) {
            return $this->helper->messageException($res['message'], $res['code']);
        }
        return $this->helper->entity($res['data'], $res['code']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
        $body = $request->validated();
        $res  = $this->service->updateDoc($id, $body, []);
        if (! $res['ok'] && $res['type'] === ResponseTypes::SERVER_ERROR) {
            return $this->helper->serverException($res['data']);
        }
        if (! $res['ok'] && $res['type'] === ResponseTypes::ERROR) {
            return $this->helper->messageException($res['message'], $res['code']);
        }
        return $this->helper->entity($res['data'], $res['code'], $res['type'], $res['message']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $res = $this->service->deleteDoc($id, []);
        if (! $res['ok'] && $res['type'] === ResponseTypes::SERVER_ERROR) {
            return $this->helper->serverException($res['data']);
        }
        if (! $res['ok'] && $res['type'] === ResponseTypes::ERROR) {
            return $this->helper->messageException($res['message'], $res['code']);
        }
        return $this->helper->entity($res['data'], $res['code'], $res['type'], $res['message']);
    }
}
