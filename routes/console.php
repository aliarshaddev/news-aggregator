<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('articles:fetch')->daily();