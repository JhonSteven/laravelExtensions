<?php

namespace ParraWeb;

use ParraWeb\ValidationRules;
use ParraWeb\UpdateAndGet;

trait ModelExtensions{
  use ValidationRules,UpdateAndGet;
}