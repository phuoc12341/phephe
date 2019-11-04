<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use App\Exceptions\Api\NotFoundException;
use App\Exceptions\Api\UnknownException;
use App\Exceptions\Api\NotOwnerException;
use App\Exceptions\Api\ActionException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DB;
use Log;

class ApiController extends Controller
{
    protected function jsonRender()
    {
        $this->compacts['code'] = 200;

        return response()->json($this->compacts);
    }

    protected function doAction(callable $callback, $action = null)
    {
        DB::beginTransaction();
        try {
            if (is_callable($callback)) {
                call_user_func_array($callback, []);
            }

            DB::commit();
        } catch (ModelNotFoundException $exception) {
            Log::error($exception->getMessage());
            DB::rollBack();

            throw new NotFoundException();
        } catch (NotOwnerException $exception) {
            Log::error($exception->getMessage());
            DB::rollBack();

            throw new NotOwnerException();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            DB::rollBack();

            if (array_key_exists($action, __('exception'))) {
                throw new ActionException($action);
            }

            throw new UnknownException($exception->getMessage(), $exception->getCode());
        }

        return $this->jsonRender();
    }
}
