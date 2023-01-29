<?php

namespace App\Http\Controllers;

use App\Actions\ContactPhoneCreateAction;
use App\Actions\ContactPhoneDeleteAction;
use App\Actions\ContactPhoneUpdateAction;
use App\DTOs\ContactPhoneCreateDTO;
use App\DTOs\ContactPhoneUpdateDTO;
use App\Http\Requests\ContactPhoneCreateFormRequest;
use App\Http\Requests\ContactPhoneUpdateFormRequest;
use App\Http\Resources\ContactPhoneResource;
use App\Http\Responses\ResourceCreatedResponse;
use App\Http\Responses\ResourceNotFoundResponse;
use App\Http\Responses\ResourceUpdatedResponse;
use App\Models\Contact;
use App\Models\ContactPhone;
use App\Services\ElasticsearchService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContactPhoneController extends Controller
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

        return ContactPhoneResource::collection($contact->phones);
    }

    /**
     * @param int|string $contactId
     * @param ContactPhoneCreateFormRequest $request
     * @param ContactPhoneCreateAction $createAction
     * @param ElasticsearchService $elasticsearchService
     * @param ResourceNotFoundResponse $notFoundResponse
     * @param ResourceCreatedResponse $createdResponse
     * @return JsonResponse
     * @throws Exception
     */
    public function store(
        int|string                    $contactId,
        ContactPhoneCreateFormRequest $request,
        ContactPhoneCreateAction      $createAction,
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

        $contactPhone = $createAction->handle(new ContactPhoneCreateDTO($contact->id, ...$request->validated()), $elasticsearchService);
        return new JsonResponse([
            'message' => $createdResponse->getMessage(),
            'data' => new ContactPhoneResource($contactPhone)
        ], $createdResponse->getStatus());
    }

    /**
     * @param int|string $contactId
     * @param int|string $id
     * @param ResourceNotFoundResponse $notFoundResponse
     * @return JsonResponse|ContactPhoneResource
     */
    public function show(int|string $contactId, int|string $id, ResourceNotFoundResponse $notFoundResponse): JsonResponse|ContactPhoneResource
    {
        $contactPhone = ContactPhone::where('contact_id', '=', $contactId)
            ->find($id);

        if (is_null($contactPhone)) {
            return new JsonResponse([
                'message' => $notFoundResponse->getMessage(),
            ], $notFoundResponse->getStatus());
        }

        return new ContactPhoneResource($contactPhone);
    }

    /**
     * @param int|string $contactId
     * @param int|string $id
     * @param ContactPhoneUpdateFormRequest $request
     * @param ContactPhoneUpdateAction $updateAction
     * @param ElasticsearchService $elasticsearchService
     * @param ResourceNotFoundResponse $notFoundResponse
     * @param ResourceUpdatedResponse $updatedResponse
     * @return JsonResponse
     * @throws Exception
     */
    public function update(
        int|string $contactId,
        int|string $id,
        ContactPhoneUpdateFormRequest $request,
        ContactPhoneUpdateAction $updateAction,
        ElasticsearchService $elasticsearchService,
        ResourceNotFoundResponse $notFoundResponse,
        ResourceUpdatedResponse $updatedResponse
    ): JsonResponse {
        $contactPhone = ContactPhone::where('contact_id', '=', $contactId)
            ->find($id);

        if (is_null($contactPhone)) {
            return new JsonResponse([
                'message' => $notFoundResponse->getMessage()
            ], $notFoundResponse->getStatus());
        }

        return new JsonResponse([
            'message' => $updatedResponse->getMessage(),
            'data' => new ContactPhoneResource($updateAction->handle($contactPhone, new ContactPhoneUpdateDTO(...$request->validated()), $elasticsearchService))
        ], $updatedResponse->getStatus());
    }

    /**
     * @param int|string $contactId
     * @param int|string $id
     * @param ContactPhoneDeleteAction $deleteAction
     * @param ElasticsearchService $elasticsearchService
     * @param ResourceNotFoundResponse $notFoundResponse
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(
        int|string               $contactId,
        int|string               $id,
        ContactPhoneDeleteAction $deleteAction,
        ElasticsearchService     $elasticsearchService,
        ResourceNotFoundResponse $notFoundResponse
    ): JsonResponse {
        $contactPhone = ContactPhone::where('contact_id', '=', $contactId)
            ->find($id);

        if (is_null($contactPhone)) {
            return new JsonResponse([
                'message' => $notFoundResponse->getMessage()
            ], $notFoundResponse->getStatus());
        }

        $deleteAction->handle($contactPhone, $elasticsearchService);

        return new JsonResponse(status: 204);
    }
}
