<?php

namespace Articulate\Command;

use Articulate\Articulate;

class Command extends Articulate
{
    const ALTER = "ALTER";
    const SELECT = "SELECT";
    const UPDATE = "UPDATE";
    const DELETE = "DELETE";
    const TRUNCATE = "TRUNCATE";
    const CREATE = "CREATE";
    const DROP = "DROP";
    const RENAME = "RENAME";
    const ADD = "ADD";
}