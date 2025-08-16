<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Common controller functionality
 */
trait ControllerHelpers
{
    /**
     * Return success response
     */
    protected function successResponse(string $message, array $data = [], int $statusCode = 200)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data
            ], $statusCode);
        }
        
        return back()->with('success', $message);
    }
    
    /**
     * Return error response
     */
    protected function errorResponse(string $message, array $errors = [], int $statusCode = 400)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors
            ], $statusCode);
        }
        
        return back()->withErrors($errors)->with('error', $message);
    }
    
    /**
     * Validate request data
     */
    protected function validateRequest(Request $request, array $rules, array $messages = []): array
    {
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            if ($request->expectsJson()) {
                throw new ValidationException($validator);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->throwResponse();
        }
        
        return $validator->validated();
    }
    
    /**
     * Get pagination parameters from request
     */
    protected function getPaginationParams(Request $request): array
    {
        return [
            'page' => $request->get('page', 1),
            'per_page' => min($request->get('per_page', 15), 100), // Max 100 items per page
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_direction' => $request->get('sort_direction', 'desc')
        ];
    }
    
    /**
     * Get search parameters from request
     */
    protected function getSearchParams(Request $request): array
    {
        return [
            'search' => $request->get('search', ''),
            'filter' => $request->get('filter', [])
        ];
    }
    
    /**
     * Handle model not found gracefully
     */
    protected function handleModelNotFound(string $modelName = 'Resource')
    {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => "{$modelName} not found"
            ], 404);
        }
        
        return redirect()->back()->with('error', "{$modelName} not found");
    }
    
    /**
     * Handle unauthorized access
     */
    protected function handleUnauthorized(string $action = 'perform this action')
    {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => "You are not authorized to {$action}"
            ], 403);
        }
        
        return redirect()->back()->with('error', "You are not authorized to {$action}");
    }
    
    /**
     * Log controller action
     */
    protected function logAction(string $action, array $data = []): void
    {
        Log::info('Controller Action', [
            'controller' => static::class,
            'action' => $action,
            'user_id' => Auth::id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'data' => $data,
            'timestamp' => now()
        ]);
    }
    
    /**
     * Build view data array
     */
    protected function buildViewData(array $data = []): array
    {
        $baseData = [
            'user' => Auth::user(),
            'currentRoute' => request()->route()->getName(),
            'timestamp' => now()
        ];
        
        return array_merge($baseData, $data);
    }
}
