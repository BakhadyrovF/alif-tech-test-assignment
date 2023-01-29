<?php

namespace App\Http\Controllers;

use App\Actions\ContactCreateAction;
use App\Actions\ContactSearchAction;
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
use App\Services\ElasticsearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Exception
     */
    public function index(Request $request, ElasticsearchService $elasticsearchService, ContactSearchAction $searchAction)
    {
        $validator = Validator::make($request->query(), [
            'search' => ['filled', 'string']
        ]);

        $contactsBuilder = Contact::with(['emails', 'phones']);

        if (isset($validator->valid()['search'])) {
            $targetIds = $searchAction->handle($validator->valid()['search'], $elasticsearchService);
            $contactsBuilder->whereIn('id', $targetIds)
                ->orderByRaw("FIELD(id, {$targetIds->implode(', ')})");
        }

        return ContactResource::collection($contactsBuilder->paginate(20));
    }

    /**
     * @param ContactCreateFormRequest $request
     * @param ContactCreateAction $createAction
     * @param ResourceCreatedResponse $createdResponse
     * @return JsonResponse
     */
    public function store(ContactCreateFormRequest $request, ContactCreateAction $createAction, ResourceCreatedResponse $createdResponse): JsonResponse
    {
        $contact = $createAction->handle(new ContactCreateDTO(...$request->validated()));
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
     * @param ContactUpdateAction $updateAction
     * @param ResourceNotFoundResponse $notFoundResponse
     * @param ResourceUpdatedResponse $updatedResponse
     * @return JsonResponse
     */
    public function update(
        ContactUpdateFormRequest $request,
        int|string               $id,
        ContactUpdateAction      $updateAction,
        ResourceNotFoundResponse $notFoundResponse,
        ResourceUpdatedResponse  $updatedResponse
    ): JsonResponse {
        $contact = Contact::find($id);

        if (is_null($contact)) {
            return new JsonResponse([
                'message' => $notFoundResponse->getMessage()
            ], 404);
        }

        return new JsonResponse([
            'message' => $updatedResponse->getMessage(),
            'data' => new ContactResource($updateAction->handle($contact, new ContactUpdateDTO(...$request->validated())))
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
