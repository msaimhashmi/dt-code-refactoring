<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

class BookingController extends Controller
{
    // Removed unnecessary comments, parentheses, and code.
    // Less the amount of code by using operators.
    // Changed all variables to camelCase and followed this pattern overall in the code.
    // Improved code format, style, and structure in a consistent and proper way for better readability.
    
    protected $repository;

    public function __construct(BookingRepository $bookingRepository)
    {
        $this->repository = $bookingRepository;
    }

    public function index(Request $request)
    {
        // Set the variables (camelCase).
        // Added null coalescing operator (??) to return an empty array if $response is null.

        $userId = $request->get('user_id');
        $userType = $request->__authenticatedUser->user_type;

        if ($userId) {
            $response = $this->repository->getUsersJobs($userId);
        } elseif ($userType == env('ADMIN_ROLE_ID') || $userType == env('SUPERADMIN_ROLE_ID')) {
            $response = $this->repository->getAll($request);
        }

        return response($response ?? []);
    }

    public function show($id)
    {
        // Added null coalescing operator (??) to return an empty array if $response is null.

        $job = $this->repository->with('translatorJobRel.user')->find($id);

        return response($job ?? []);
    }

    public function store(Request $request)
    {
        // separates the retrieval of the authenticated user in the (camelCase) variable from the call to the repository's store method to make the code more readable and easier.
        // Added null coalescing operator (??) to return an empty array if $response is null.

        $data = $request->all();
        $authenticatedUser = $request->__authenticatedUser;

        $response = $this->repository->store($authenticatedUser, $data);

        return response($response ?? []);
    }

    public function update($id, Request $request)
    {
        // Removed the array_except() call and replaced it with except() method, which is the prebuilt function of laravel.
        // Removed the $request->all() because this is not necessary to add here because except() or array_except() mehtod get all data except the mentioned fields.
        // Moved the $data variable assignment to the top for better readability.
        // Added null coalescing operator (??) to return an empty array if $response is null.

        $data = $request->except(['_token', 'submit']);
        $cuser = $request->__authenticatedUser;

        $response = $this->repository->updateJob($id, $data, $cuser);

        return response($response ?? []);
    }

    public function immediateJobEmail(Request $request)
    {
        // Removed the unused variable $adminSenderEmail.
        // Added null coalescing operator (??) to return an empty array if $response is null.

        $data = $request->all();

        $response = $this->repository->storeJobEmail($data);

        return response($response ?? []);
    }


    public function getHistory(Request $request)
    {
        // Moved the variable assignment outside of the if statement to improve readability.
        // Changed the variable name to camelCase as per the coding style format.
        // Removed the unnecessary return statement to follow a consistent code pattern (which is used from the start) for better readability and ease of understanding and remembrance.

        $userId = $request->get('user_id');

        if($userId) {
            $response = $this->repository->getUsersJobsHistory($userId, $request);
        }

        return response($response ?? []);
    }

    public function acceptJob(Request $request)
    {
        // Added null coalescing operator (??) to return an empty array if $response is null.

        $data = $request->all();
        $user = $request->__authenticatedUser;

        $response = $this->repository->acceptJob($data, $user);

        return response($response ?? []);
    }

    public function acceptJobWithId(Request $request)
    {
        // Added null coalescing operator (??) to return an empty array if $response is null.

        $data = $request->get('job_id');
        $user = $request->__authenticatedUser;

        $response = $this->repository->acceptJobWithId($data, $user);

        return response($response ?? []);
    }

    public function cancelJob(Request $request)
    {
        // Added null coalescing operator (??) to return an empty array if $response is null.

        $data = $request->all();
        $user = $request->__authenticatedUser;

        $response = $this->repository->cancelJobAjax($data, $user);

        return response($response ?? []);
    }

    public function endJob(Request $request)
    {
        // Added null coalescing operator (??) to return an empty array if $response is null.

        $data = $request->all();

        $response = $this->repository->endJob($data);

        return response($response ?? []);
    }

    public function customerNotCall(Request $request)
    {
        // Added null coalescing operator (??) to return an empty array if $response is null.

        $data = $request->all();

        $response = $this->repository->customerNotCall($data);

        return response($response ?? []);
    }

    public function getPotentialJobs(Request $request)
    {
        // Removed the unused variable $data.
        // Added null coalescing operator (??) to return an empty array if $response is null.

        $user = $request->__authenticatedUser;

        $response = $this->repository->getPotentialJobs($user);

        return response($response ?? []);
    }

    public function distanceFeed(Request $request)
    {
        // This method looks complicated due to the old way and the presence of unnecessary statements which is making the code harder to manage. It is improved in the following ways I mentioned:

        // Replace all isset() blocks with the null coalescing operator (??) to provide default values if the keys are not set.
        // Remove the unnecessary else blocks after each if statement.
        // Use ternary operators instead of if-else blocks to simplify the logic and reduce the amount of code.
        // Separate condition to return add comment response for better readability.
        // Use the update() method with an associative array to simplify the update queries.

        $data = $request->all();

        $distance = $data['distance'] ?? '';
        $time = $data['time'] ?? '';
        $jobid = $data['jobid'] ?? '';
        $session = $data['session_time'] ?? '';
        $flagged = ($data['flagged'] == 'true') ? 'yes' : 'no';
        $manually_handled = ($data['manually_handled'] == 'true') ? 'yes' : 'no';
        $by_admin = ($data['by_admin'] == 'true') ? 'yes' : 'no';
        $admincomment = $data['admincomment'] ?? '';

        if ($data['flagged'] == 'true' && $data['admincomment'] == '') {
            return response('Please, add comment');
        }

        if ($time || $distance) {
            Distance::where('job_id', $jobid)->update(['distance' => $distance, 'time' => $time]);
        }

        if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {
            Job::where('id', $jobid)->update([
                'admin_comments' => $admincomment,
                'flagged' => $flagged,
                'session_time' => $session,
                'manually_handled' => $manually_handled,
                'by_admin' => $by_admin,
            ]);
        }

        return response('Record updated!');
    }

    public function reopen(Request $request)
    {
        // Added null coalescing operator (??) to return an empty array if $response is null.

        $data = $request->all();
        $response = $this->repository->reopen($data);

        return response($response ?? []);
    }

    public function resendNotifications(Request $request)
    {
        // Add try-catch here also to resend notifications as used in resendSMSNotifications().

        $data = $request->all();
        $job = $this->repository->find($data['jobid']);
        $job_data = $this->repository->jobToData($job);

        try {
            $this->repository->sendNotificationTranslator($job, $job_data, '*');
            return response(['success' => 'Push sent'], 200);
        } catch (\Exception $e) {
            return response(['error' => 'Failed to send notification', 'message' => $e->getMessage()], 500);
        }
    }

    public function resendSMSNotifications(Request $request)
    {
        // For a more informative response for the catch block. Instead of returning ['success' => $e->getMessage()], I return an error message with a status code of 500 for a server error and a success message with a status code of 200 for success.

        $data = $request->all();
        $job = $this->repository->find($data['jobid']);
        $job_data = $this->repository->jobToData($job);

        try {
            $this->repository->sendSMSNotificationToTranslator($job);
            return response(['success' => 'SMS sent'], 200);
        } catch (\Exception $e) {
            return response(['error' => 'Failed to send SMS notification', 'message' => $e->getMessage()], 500);
        }
    }

}
