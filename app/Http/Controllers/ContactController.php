<?php

namespace App\Http\Controllers;

use App\Actions\ContactCreateAction;
use App\Actions\ContactDeleteAction;
use App\Actions\ContactSearchAction;
use App\Actions\ContactUpdateAction;
use App\DTOs\ContactCreateDTO;
use App\DTOs\ContactSearchDTO;
use App\DTOs\ContactUpdateDTO;
use App\Http\Requests\ContactCreateFormRequest;
use App\Http\Requests\ContactUpdateFormRequest;
use App\Http\Resources\ContactResource;
use App\Http\Responses\ResourceCreatedResponse;
use App\Http\Responses\ResourceNotFoundResponse;
use App\Http\Responses\ResourceUpdatedResponse;
use App\Models\Contact;
use App\Services\ElasticsearchService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     * @throws Exception
     */
    public function index(Request $request, ElasticsearchService $elasticsearchService, ContactSearchAction $searchAction)
    {
        $validator = Validator::make($request->query(), [
            'query' => ['filled', 'string']
        ]);

        $contactsBuilder = Contact::with(['emails', 'phones']);

        if (isset($validator->valid()['query'])) {
            $targetIds = $searchAction->handle(new ContactSearchDTO(...$validator->valid()), $elasticsearchService);
            $contactsBuilder->whereIn('id', $targetIds)
                ->orderByRaw("FIELD(id, {$targetIds->implode(', ')})");
        }

        return ContactResource::collection($contactsBuilder->paginate(20));
    }

    /**
     * @param ContactCreateFormRequest $request
     * @param ContactCreateAction $createAction
     * @param ElasticsearchService $elasticsearchService
     * @param ResourceCreatedResponse $createdResponse
     * @return JsonResponse
     * @throws Exception
     */
    public function store(
        ContactCreateFormRequest  $request,
        ContactCreateAction       $createAction,
        ElasticsearchService      $elasticsearchService,
        ResourceCreatedResponse   $createdResponse
    ): JsonResponse {
        $contact = $createAction->handle(new ContactCreateDTO(...$request->validated()), $elasticsearchService);

        return new JsonResponse([
            'message' => $createdResponse->getMessage(),
            'data' => new ContactResource($contact)
        ], $createdResponse->getStatus());
    }

    /**
     * @param int|string $id
     * @param ResourceNotFoundResponse $notFoundResponse
     * @return JsonResponse|ContactResource
     */
    public function show(int|string $id, ResourceNotFoundResponse $notFoundResponse): JsonResponse|ContactResource
    {
        $contact = Contact::find($id);

        if (is_null($contact)) {
            return new JsonResponse([
                'message' => $notFoundResponse->getMessage()
            ], $notFoundResponse->getStatus());
        }

        return new ContactResource($contact);
    }

    /**
     * @param ContactUpdateFormRequest $request
     * @param int|string $id
     * @param ContactUpdateAction $updateAction
     * @param ElasticsearchService $elasticsearchService
     * @param ResourceNotFoundResponse $notFoundResponse
     * @param ResourceUpdatedResponse $updatedResponse
     * @return JsonResponse
     * @throws Exception
     */
    public function update(
        ContactUpdateFormRequest           $request,
        int|string                         $id,
        ContactUpdateAction                $updateAction,
        ElasticsearchService               $elasticsearchService,
        ResourceNotFoundResponse           $notFoundResponse,
        ResourceUpdatedResponse            $updatedResponse
    ): JsonResponse {
        $contact = Contact::find($id);

        if (is_null($contact)) {
            return new JsonResponse([
                'message' => $notFoundResponse->getMessage()
            ], $notFoundResponse->getStatus());
        }


        return new JsonResponse([
            'message' => $updatedResponse->getMessage(),
            'data' => new ContactResource($updateAction->handle($contact, new ContactUpdateDTO(...$request->validated()), $elasticsearchService))
        ], $updatedResponse->getStatus());
    }

    /**
     * @param int|string $id
     * @param ContactDeleteAction $deleteAction
     * @param ElasticsearchService $elasticsearchService
     * @param ResourceNotFoundResponse $notFoundResponse
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(
        int|string               $id,
        ContactDeleteAction      $deleteAction,
        ElasticsearchService     $elasticsearchService,
        ResourceNotFoundResponse $notFoundResponse
    ): JsonResponse
    {
        $contact = Contact::find($id);

        if (is_null($contact)) {
            return new JsonResponse([
                'message' => $notFoundResponse->getMessage()
            ], $notFoundResponse->getStatus());
        }

        $deleteAction->handle($contact, $elasticsearchService);

        return new JsonResponse(status: 204);
    }
}
