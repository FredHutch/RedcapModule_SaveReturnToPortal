<?php
// Set the namespace defined in your config file
namespace FredHutchNamespace\SaveReturnToPortalClass;
// The next 2 lines should always be included and be the same in every module
use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;

use \REDCap;

// Declare your module class, which must extend AbstractExternalModule 
class SaveReturnToPortalClass extends AbstractExternalModule {

    public function __construct(){
        parent::__construct();
    }

    function redcap_save_record($project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id, $repeat_instance)
    {
        // REDCap::logEvent(__FUNCTION__, "$record - $instrument - $event_id");

        // only fire on surveys
        if (empty($survey_hash)) return;

        if (isset($_GET['__return'])) {
            // This is a save and return
            $this->exitAfterHook();
            $this->redirectToPortal();
        }
    }
    
    function redirect() {
        // If contents already output, use javascript to redirect instead
        if (headers_sent())
        {
            echo "<script type='text/javascript'>window.location.href=\"$url\";</script>";
        }
        // Redirect using PHP
        else
        {
            header("Location: $url");
        }
    }

    function redirectToPortal() {
        $addBtnUrl = $this->getProjectSetting('global_custom_button_url');
        $this->redirect($addBtnUrl);
    }

    function redcap_survey_complete($project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id, $repeat_instance)
    {
        // REDCap::logEvent("Survey Complete Hook Called", "$record - $instrument - $event_id");
        $addBtnUrl = $this->getProjectSetting('global_custom_button_url');
        $this->exitAfterHook();
        $this->redirect($addBtnUrl);
    }

    function redcap_survey_page($project_id, $record, $instrument, $event_id, $group_id, $repeat_instance) 
    {
        $addBtn = $this->getProjectSetting('global_show_custom_button');
        $addBtnText = $this->getProjectSetting('global_custom_button_text');
        $addBtnUrl = $this->getProjectSetting('global_custom_button_url');

        if (isset($_GET['sq'])) {
            // on the queue page
            $this-redirectToPortal();
            $this->exitAfterHook();
            return;
        }
        ?>
        <script type="text/javascript">
        $(document).ready(function() {
            if ($("button[name=\'submit-btn-saverecord\']").is(':visible')
                && $("button[name=\'submit-btn-saverecord\']").text()=='Submit') {
                $("button[name=\'submit-btn-savereturnlater\']").hide();
            }
        });
        </script>
        <?php	
    }
}
