<?php

namespace App\Http\Controllers;

use App\Actions\ContactEmailCreateAction;
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
     * @param ResourceNotFoundResponse $notFoundResponse
     * @param ResourceCreatedResponse $createdResponse
     * @return JsonResponse
     */
    public function store(
        int|string                    $contactId,
        ContactEmailCreateFormRequest $request,
        ContactEmailCreateAction      $createAction,
        ResourceNotFoundResponse      $notFoundResponse,
        ResourceCreatedResponse       $createdResponse
    ): JsonResponse {
        $contact = Contact::find($contactId);

        if (is_null($contact)) {
            return new JsonResponse([
                'message' => $notFoundResponse->getMessage()
            ], $notFoundResponse->getStatus());
        }

        $contactEmail = $createAction->handle(new ContactEmailCreateDTO($contact->id, ...$request->validated()));
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
     * @param ResourceNotFoundResponse $notFoundResponse
     * @param ResourceUpdatedResponse $updatedResponse
     * @return JsonResponse
     */
    public function update(
        int|string                    $contactId,
        int|string                    $id,
        ContactEmailUpdateFormRequest $request,
        ContactEmailUpdateAction      $updateAction,
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
            'data' => new ContactEmailResource($updateAction->handle($contactEmail, new ContactEmailUpdateDTO(...$request->validated())))
        ], $updatedResponse->getStatus());
    }

    /**
     * @param int|string $contactId
     * @param int|string $id
     * @param ResourceNotFoundResponse $notFoundResponse
     * @return JsonResponse
     */
    public function destroy(int|string $contactId, int|string $id, ResourceNotFoundResponse $notFoundResponse)
    {
        $contactEmail = ContactEmail::where('contact_id', '=', $contactId)
            ->find($id);

        if (is_null($contactEmail)) {
            return new JsonResponse([
                'message' => $notFoundResponse->getMessage()
            ], $notFoundResponse->getStatus());
        }

        $contactEmail->delete();

        return new JsonResponse(status: 204);
    }
}
