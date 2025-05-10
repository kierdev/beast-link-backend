<?php

namespace App\Http\Controllers;

use App\Models\DocumentList;
use App\Http\Requests\StoreDocumentListRequest;
use App\Http\Requests\UpdateDocumentListRequest;
use Illuminate\Http\Request;

class DocumentListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documentLists = DocumentList::all();
        return ['documentLists' => $documentLists, 'message' => 'Document Lists retrieved successfully'];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_name' => 'required|string|unique:document_lists,document_name',
        ], [
            'document_name.required' => 'The document name field is required.',
            'document_name.string' => 'The document name must be a string.',
            'document_name.unique' => 'The document name must be unique.',
        ]);

        $documentList = DocumentList::create($validated);
        return ['documentList' => $documentList, 'message' => 'Document List created successfully'];
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentList $documentList)
    {
        return ['documentList' => $documentList, 'message' => 'Document List retrieved successfully'];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DocumentList $documentList)
    {
        $validated = $request->validate([
            'document_name' => 'required|string|unique:document_lists,document_name,' . $documentList->id,
        ], [
            'document_name.required' => 'The document name field is required.',
            'document_name.string' => 'The document name must be a string.',
            'document_name.unique' => 'The document name must be unique.',
        ]);

        $documentList->update($validated);
        return ['documentList' => $documentList, 'message' => 'Document List updated successfully'];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentList $documentList)
    {
        $documentList->delete();
        return ['message' => 'Document List deleted successfully'];
    }
}
