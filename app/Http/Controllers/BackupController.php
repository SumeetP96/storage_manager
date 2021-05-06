<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use App\Services\Response\ResponseService;

class BackupController extends Controller
{

    /**
     * @var \App\Services\Response\ResponseService
     */
    private $responseService;

    /**
     * Default path for data backup
     * @var String
     */
    private $defaultBackupPath;

    /**
     * Default path for application backup
     * @var String
     */
    private $backupStorage;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->responseService = new ResponseService;
        $this->defaultBackupPath = 'D:\Storage Manager\Backups';
        $this->backupStorage = str_replace('/', '\\', base_path() . '\\storage\\app\\Storage-Manager');
    }

    /**
     * Backup data
     *
     * @param mixed
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        for($i = 'a'; $i <= 'z'; $i++) {
            $path = stristr($request->path, $i . ':');
            if ($path) break;
        }

        if (!$path) {
            return $this->responseService->error([
                'path'  => ['Invalid folder path.']
            ]);
        }

        if ($request->path != $this->defaultBackupPath) $request->path .= '\\Storage Manager\\Backups';

        Artisan::call('backup:clean');
        Artisan::call('backup:run');

        $command = 'call ROBOCOPY "'. $this->backupStorage .'" "'. $request->path .'" /MIR';

        system($command, $output);

        return $this->responseService->success(null, $output);
    }

    /**
     * Get default backup path
     *
     * @return \Illuminate\Http\Response
     */
    public function getDefaultPath()
    {
        return response()->json(['path' => $this->defaultBackupPath], 200);
    }
}
