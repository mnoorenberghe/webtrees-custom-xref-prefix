<?php

namespace Vesta\ControlPanelUtils\Model;

class ControlPanelPreferences {

  private $sections;

  /**
   * @return ControlPanelSection[]
   */
  public function getSections() {
    return $this->sections;
  }

  public function __construct($sections) {
    $this->sections = $sections;
  }

}
