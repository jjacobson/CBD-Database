<?php

class Password
{

  function hash($pass)
  {
    return wp_hash_password($pass);
  }

  function verify($pass, $check)
  {
    return wp_verify_password($pass, $check);
  }

}



 ?>