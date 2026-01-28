<?php

namespace App\Traits;

use Illuminate\Http\RedirectResponse;

/**
 * Trait for standardized web responses
 */
trait WebResponse
{
    /**
     * Redirect with success message
     */
    public function successRedirect($route, $message = 'Operation completed successfully', $params = [])
    {
        return redirect()->route($route, $params)
            ->with('success', $message);
    }

    /**
     * Redirect with error message
     */
    public function errorRedirect($message = 'An error occurred', $params = [])
    {
        return back()
            ->withInput()
            ->with('error', $message);
    }

    /**
     * Redirect with validation errors
     */
    public function validationErrorRedirect($errors)
    {
        return back()
            ->withInput()
            ->withErrors($errors);
    }

    /**
     * Redirect back with success
     */
    public function backWithSuccess($message = 'Operation completed successfully')
    {
        return back()->with('success', $message);
    }

    /**
     * Redirect back with error
     */
    public function backWithError($message = 'An error occurred')
    {
        return back()->with('error', $message);
    }
}
