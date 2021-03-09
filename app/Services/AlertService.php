<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Field;
use App\Models\Node;
use App\Repositories\Contracts\AlertRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AlertService
{
    private $alertRepository;
    private $nodeService;

    /**
     * Create a new service instance.
     * @param  \App\Repositories\Contracts\AlertRepository $repository
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function __construct(AlertRepository $alertRepository)
    {
        $this->alertRepository = $alertRepository;
    }

    public function createAlert(Field $field, $timestamp, $errorLevel)
    {
        $alert = $field->alerts()->get()->last();
        //if alert already reported -> do nothing
        if ($alert == null || Carbon::parse($timestamp)->greaterThan(Carbon::parse($alert->exceed_timestamp))) {
            $alert = new Alert([
                'exceed_timestamp' => $timestamp,
                'field_id' => $field->id,
                'error_level' => $errorLevel,
            ]);
            return $this->alertRepository->create($alert->toArray());
        }
        return $alert;
    }

    public function AlertReported(Alert $alert)
    {
        $alert->send = true;
        //must have otherwise current time stamp will be safe in exceed_timestamp
        $alert->exceed_timestamp = Carbon::parse($alert->exceed_timestamp)->format('Y-m-d H:i:s.u0');
        return $this->alertRepository->update($alert->id, $alert->toArray());
    }

    public function Update(Request $request, Alert $alert)
    {
        $alert->field_id = $request->field_id;
        $alert->exceed_timestamp = $request->exceed_timestamp;
        $this->alertRepository->update($alert->id, $alert->toArray());
    }

    public function Delete(Alert $alert)
    {
        $this->alertRepository->delete($alert->id);
    }
    
    public function Warning(Alert $alert){
        $alert->error_level = 1;
        $this->alertRepository->update($alert->id, $alert->toArray());
    }

    public function Error(Alert $alert){
        $alert->error_level = 2;
        $this->alertRepository->update($alert->id, $alert->toArray());
    }
}
