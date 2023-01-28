<?php

namespace App\Http\Controllers;

use App\Actions\ContactCreateAction;
use App\Actions\ContactUpdateAction;
use App\DTOs\ContactCreateDTO;
use App\DTOs\ContactUpdateDTO;
use App\Http\Requests\ContactCreateFormRequest;
use App\Http\Requests\ContactUpdateFormRequest;
use App\Http\Resources\ContactResource;
use App\Http\Responses\ResourceCreatedResponse;
use App\Http\Responses\ResourceNotFoundResponse;
use App\Http\Responses\ResourceUpdatedResponse;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * @param ContactCreateFormRequest $request
     * @param ContactCreateAction $action
     * @param ResourceCreatedResponse $createdResponse
     * @return JsonResponse
     */
    public function store(ContactCreateFormRequest $request, ContactCreateAction $action, ResourceCreatedResponse $createdResponse): JsonResponse
    {
        $contact = $action->handle(new ContactCreateDTO(...$request->validated()));
        return new JsonResponse([
            'message' => $createdResponse->getMessage(),
            'data' => new ContactResource($contact)
        ], 201);
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
            ], 404);
        }

        return new ContactResource($contact);
    }

    /**
     * @param ContactUpdateFormRequest $request
     * @param int|string $id
     * @param ContactUpdateAction $action
     * @param ResourceNotFoundResponse $notFoundResponse
     * @param ResourceUpdatedResponse $updatedResponse
     * @return JsonResponse
     */
    public function update(
        ContactUpdateFormRequest $request,
        int|string $id,
        ContactUpdateAction $action,
        ResourceNotFoundResponse $notFoundResponse,
        ResourceUpdatedResponse $updatedResponse
    ): JsonResponse {
        $contact = Contact::find($id);

        if (is_null($contact)) {
            return new JsonResponse([
                'message' => $notFoundResponse->getMessage()
            ], 404);
        }

        return new JsonResponse([
            'message' => $updatedResponse->getMessage(),
            'data' => new ContactResource($action->handle($contact, new ContactUpdateDTO(...$request->validated())))
        ]);
    }

    /**
     * @param int|string $id
     * @param ResourceNotFoundResponse $notFoundResponse
     * @return JsonResponse
     */
    public function destroy(int|string $id, ResourceNotFoundResponse $notFoundResponse): JsonResponse
    {
        $contact = Contact::find($id);

        if (is_null($contact)) {
            return new JsonResponse([
                'message' => $notFoundResponse->getMessage()
            ], 404);
        }

        $contact->delete();

        return new JsonResponse(status: 204);
    }
}
