<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Get all invoices
     * 
     * @queryParam status Filter invoices by status. Must be one of: draft, sent, paid, overdue, cancelled. Example: paid
     * @queryParam due_date_from Filter invoices with due date after this date. Example: 2024-05-01
     * @queryParam due_date_to Filter invoices with due date before this date. Example: 2024-05-31
     * 
     * @response scenario=success {
     *  "data": [
     *    {
     *      "id": 1,
     *      "invoice_number": "INV-001",
     *      "customer_name": "John Doe",
     *      "title": "Monthly Service",
     *      "description": "Monthly maintenance service",
     *      "status": "paid",
     *      "due_date": "2024-05-15T00:00:00",
     *      "created_at": "2024-05-05T12:00:00",
     *      "updated_at": "2024-05-05T12:00:00"
     *    }
     *  ]
     * }
     */
    public function index(Request $request)
    {
        $query = Invoice::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('due_date_from')) {
            $query->where('due_date', '>=', $request->due_date_from);
        }

        if ($request->has('due_date_to')) {
            $query->where('due_date', '<=', $request->due_date_to);
        }

        return response()->json($query->get());
    }

    /**
     * Get a specific invoice
     * 
     * @urlParam id integer required The ID of the invoice. Example: 1
     * 
     * @response scenario=success {
     *  "id": 1,
     *  "invoice_number": "INV-001",
     *  "customer_name": "John Doe",
     *  "title": "Monthly Service",
     *  "description": "Monthly maintenance service",
     *  "status": "paid",
     *  "due_date": "2024-05-15T00:00:00",
     *  "created_at": "2024-05-05T12:00:00",
     *  "updated_at": "2024-05-05T12:00:00"
     * }
     * 
     * @response status=404 scenario="invoice not found" {
     *  "message": "No query results for model [App\\Models\\Invoice] 1"
     * }
     */
    public function show(Invoice $invoice)
    {
        return response()->json($invoice);
    }

    /**
     * Create a new invoice
     * 
     * @bodyParam invoice_number string required The invoice number. Example: INV-001
     * @bodyParam customer_name string required The customer's name. Example: John Doe
     * @bodyParam title string required The invoice title. Example: Monthly Service
     * @bodyParam description string The invoice description. Example: Monthly maintenance service
     * @bodyParam status string required The invoice status. Must be one of: draft, sent, paid, overdue, cancelled. Example: paid
     * @bodyParam due_date string The due date of the invoice. Example: 2024-05-15T00:00:00
     * 
     * @response status=201 scenario=success {
     *  "id": 1,
     *  "invoice_number": "INV-001",
     *  "customer_name": "John Doe",
     *  "title": "Monthly Service",
     *  "description": "Monthly maintenance service",
     *  "status": "paid",
     *  "due_date": "2024-05-15T00:00:00",
     *  "created_at": "2024-05-05T12:00:00",
     *  "updated_at": "2024-05-05T12:00:00"
     * }
     * 
     * @response status=422 scenario="validation error" {
     *  "message": "The given data was invalid.",
     *  "errors": {
     *    "invoice_number": ["The invoice number field is required."],
     *    "customer_name": ["The customer name field is required."],
     *    "title": ["The title field is required."],
     *    "status": ["The status field is required."]
     *  }
     * }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string',
            'customer_name' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'due_date' => 'nullable|date',
        ]);

        $invoice = Invoice::create($validated);

        return response()->json($invoice, 201);
    }

    /**
     * Delete an invoice
     * 
     * @urlParam id integer required The ID of the invoice. Example: 1
     * 
     * @response scenario=success {
     *  "message": "Invoice deleted successfully",
     *  "id": 1
     * }
     * 
     * @response status=404 scenario="invoice not found" {
     *  "message": "No query results for model [App\\Models\\Invoice] 1"
     * }
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response()->json([
            'message' => 'Invoice deleted successfully',
            'id' => $invoice->id
        ], 200);
    }
} 