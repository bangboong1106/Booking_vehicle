<?php
function buildDashBoardUrl()
{
    return route('dashboard.index', buildDashBoardParamsDefault());
}

function buildDashBoardParamsDefault()
{
    return [];
}
