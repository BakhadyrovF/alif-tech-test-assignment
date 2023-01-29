<?php

namespace App\Http\Controllers;

use App\Actions\ContactEmailCreateAction;
use App\Actions\ContactEmailDeleteAction;
use App\Actions\ContactEmailUpdateAction;
use App\DTOs\ContactEmailCreateDTO;
use App\DTOs\ContactEmailUpdateDTO;
use App\Http\Requests\ContactEmailCreateFormRequest;
use App\Http\Requests\ContactEmailUpdateFormRequest;
use App\Http\Resources\ContactEmailResource;
use App\Http\Responses\ResourceCreatedResponse;
use App\Http\Responses\ResourceNotFoundResponse;
use App\Http\Responses\ResourceUpdatedResponse;
use App\Models\Contact;
use App\Models\ContactEmail;
use App\Services\ElasticsearchService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContactEmailController extends Controller
{
    /**
     * @param int|string $contactId
     * @param ResourceNotFoundResponse $notFoundResponse
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(int|string $contactId, ResourceNotFoundResponse $notFoundResponse): JsonResponse|AnonymousResourceCollection
    {
        $contact = Contact::find($contactId);

        if (is_null($contact)) {
            return new JsonResponse([
                'message' => $notFoundResponse->getMessage()
            ], $notFoundResponse->getStatus());
        }

        return ContactEmailResource::collection($contact->emails);
    }

    /**
     * @param int|string $contactId
     * @param ContactEmailCreateFormRequest $request
     * @param ContactEmailCreateAction $createAction
     * @param ElasticsearchService $elasticsearchService
     * @param ResourceNotFoundResponse $notFoundResponse
     * @param ResourceCreatedResponse $createdResponse
     * @return JsonResponse
     * @throws Exception
     */
    public function store(
        int|string                    $contactId,
        ContactEmailCreateFormRequest $request,
        ContactEmailCreateAction      $createAction,
        ElasticsearchService          $elasticsearchService,
        ResourceNotFoundResponse      $notFoundResponse,
        ResourceCreatedResponse       $createdResponse
    ): JsonResponse {
        $contact = Contact::find($contactId);

        if (is_null($contact)) {
            return new JsonResponse([
                'message' => $notFoundResponse->getMessage()
            ], $notFoundResponse->getStatus());
        }

        $contactEmail = $createAction->handle(new ContactEmailCreateDTO($contact->id, ...$request->validated()), $elasticsearchService);
        return new JsonResponse([
            'message' => $createdResponse->getMessage(),
            'data' => new ContactEmailResource($contactEmail)
        ], $createdResponse->getStatus());
    }

    /**
     * @param int|string $contactId
     * @param int|string $id
     * @param ResourceNotFoundResponse $notFoundResponse
     * @return JsonResponse|ContactEmailResource
     */
    public function show(int|string $contactId, int|string $id, ResourceNotFoundResponse $notFoundResponse): JsonResponse|ContactEmailResource
    {
        $contactEmail = ContactEmail::where('contact_id', '=', $contactId)
            ->find($id);

        if (is_null($contactEmail)) {
            return new JsonResponse([
                'message' => $notFoundResponse->getMessage()
            ], $notFoundResponse->getStatus());
        }

        return new ContactEmailResource($contactEmail);
    }

    /**
     * @param int|string $contactId
     * @param int|string $id
     * @param ContactEmailUpdateFormRequest $request
     * @param ContactEmailUpdateAction $updateAction
     * @param ElasticsearchService $elasticsearchService
     * @param ResourceNotFoundResponse $notFoundResponse
     * @param ResourceUpdatedResponse $updatedResponse
     * @return JsonResponse
     * @throws Exception
     */
    public function update(
        int|string                    $contactId,
        int|string                    $id,
        ContactEmailUpdateFormRequest $request,
        ContactEmailUpdateAction      $updateAction,
        ElasticsearchService          $elasticsearchService,
        ResourceNotFoundResponse      $notFoundResponse,
        ResourceUpdatedResponse       $updatedResponse
    ): JsonResponse {
        $contactEmail = ContactEmail::where('contact_id', '=', $contactId)
            ->find($id);

        if (is_null($contactEmail)) {
            return new JsonResponse([
                'message' => $notFoundResponse->getMessage()
            ], $notFoundResponse->getStatus());
        }

        return new JsonResponse([
            'message' => $updatedResponse->getMessage(),
            'data' => new ContactEmailResource($updateAction->handle($contactEmail, new ContactEmailUpdateDTO(...$request->validated()), $elasticsearchService))
        ], $updatedResponse->getStatus());
    }

    /**
     * @param int|string $contactId
     * @param int|string $id
     * @param ContactEmailDeleteAction $deleteAction
     * @param ElasticsearchService $elasticsearchService
     * @param ResourceNotFoundResponse $notFoundResponse
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(
        int|string               $contactId,
        int|string               $id,
        ContactEmailDeleteAction $deleteAction,
        ElasticsearchService     $elasticsearchService,
        ResourceNotFoundResponse $notFoundResponse,
    ) {
        $contactEmail = ContactEmail::where('contact_id', '=', $contactId)
            ->find($id);

        if (is_null($contactEmail)) {
            return new JsonResponse([
                'message' => $notFoundResponse->getMessage()
            ], $notFoundResponse->getStatus());
        }

        $deleteAction->handle($contactEmail, $elasticsearchService);

        return new JsonResponse(status: 204);
    }
}
