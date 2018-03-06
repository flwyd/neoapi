<?php
/*****************************************************************************
 *  FILE:  config.php
 *
 * Copyright 2010 black rock city, llc
 *
 * Licensed under the Apache License, Version 2.0  (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 ****************************************************************************/

function configureString($name, $default, $envName = NULL) {
    global $configValues;

    if ($envName !== NULL && isset($_SERVER[$envName])) {
        $configValues[$name] = $_SERVER[$envName];
    } else {
        $configValues[$name] = $default;
    }
}

function configureBoolean($name, $default, $envName = NULL) {
    global $configValues;

    if ($envName !== NULL && isset($_SERVER[$envName])) {
        $configValues[$name] = filter_var(
            $_SERVER[$envName], FILTER_VALIDATE_BOOLEAN
        );
    } else {
        $configValues[$name] = $default;
    }
}

function configureInteger($name, $default, $envName = NULL) {
    global $configValues;

    if ($envName !== NULL && isset($_SERVER[$envName])) {
        $configValues[$name] = (int)$_SERVER[$envName];
    } else {
        $configValues[$name] = $default;
    }
}

// NOTE: Entries in local.config.php (if any) will overwrite settings here.
// local.config.php is to be customized on each machine, and should not be
// checked in to the source repository.

configureString('VCSRevision', 'DEVELOPMENT');

// when the system is taken on-site, set OnPlaya = TRUE on the server that will
// be in Black Rock City and set ReadOnly = TRUE on the system that remains
// available to the internet so folks can check their schedules
configureBoolean('OnPlaya' , FALSE, 'RANGER_CLUBHOUSE_ON_PLAYA' );
configureBoolean('ReadOnly', FALSE, 'RANGER_CLUBHOUSE_READ_ONLY');

configureString(
    'PhotoSource',
    'Local',
    'RANGER_CLUBHOUSE_PHOTO_SOURCE'
);
configureBoolean(
    'PhotoUploadEnable',
    TRUE,
    'RANGER_CLUBHOUSE_PHOTO_ENABLE_UPLOAD'
);
configureString(
    'LambaseStatusUrl',
    'http://www.tanis.com/brclam/webservice.cfc',
    'RANGER_CLUBHOUSE_LAMBASE_STATUS_URL'
);
configureString(
    'LambaseReportUrl',
    'http://www.tanis.com/brclam/webservice_rpt.cfc',
    'RANGER_CLUBHOUSE_LAMBASE_REPORT_URL'
);
configureString(
    'LambaseImageUrl',
    'http://www.lambase.com/lam_photos/rangers',
    'RANGER_CLUBHOUSE_LAMBASE_IMAGE_URL'
);
configureString(
    'LambaseJumpinUrl',
    'http://tanis.com/brclam/jumpin_ranger.cfm',
    'RANGER_CLUBHOUSE_LAMBASE_JUMP_URL'
);

configureBoolean(
    'TimesheetCorrectionEnable',
    TRUE,
    'RANGER_CLUBHOUSE_TIMESHEET_CORRECTION_ENABLE'
);
configureString(
    'TimesheetCorrectionBaseUrl',
    'https://docs.google.com/forms/d/1kv5M79N_0LRoSsElRqfZhWGtG2kFn98OuO8_ST4c-Vg/viewform?entry.81809784=',
    'RANGER_CLUBHOUSE_TIMESHEET_CORRECTION_BASE_URL'
);

// Suggestion responses spreadsheet is in the Ranger Teams > Ranger Tech > 2015 Event folder in the burningman.org Google Drive
configureString(
    'ClubhouseSuggestionUrlTemplate',
    'https://docs.google.com/forms/d/1Rox154ty2thJThS5KE-zapNKi4Rcsjgg094YyoTqeWQ/viewform?entry.561095571={callsign}&entry.1642048126={email}',
    'RANGER_CLUBHOUSE_TIMESHEET_CORRECTION_TEMPLATE_URL'
);

// The 2015 shift log is the "Messages" section on the HQShiftLog user
configureString(
    'ShiftLogUrl',
    '?DMSc=person&DMSm=select&personId=3951#messages',
    'RANGER_CLUBHOUSE_SHIFT_LOG_URL'
);

// How to join Ranger special teams
configureString(
    'JoiningRangerSpecialTeamsUrl',
    'https://docs.google.com/document/d/1xEVnm1SdcyvLnsUYwL5v_WxO1zy3yhMkAmbXIU_0yqc',
    'RANGER_CLUBHOUSE_SPECIAL_TEAMS_URL'
);

// Motorpool policy form
configureString(
    'MotorpoolPolicyUrl',
    "https://docs.google.com/forms/d/1wEn544ZcpdWuvSxCYpoX5uAS_CeXitEsJJGwaPCpl-I/viewform",
    'RANGER_CLUBHOUSE_MOTORPOOL_POLICY_URL'
);

// Manual review Google sheet
configureBoolean(
    'ManualReviewLinkEnable',
    FALSE,
    'RANGER_CLUBHOUSE_REVIEW_ENABLE'
);
configureInteger(
    'ManualReviewProspectiveAlphaLimit',
    177,
    'RANGER_CLUBHOUSE_REVIEW_MAX_ALPHAS'
);
configureString(
    'ManualReviewGoogleFormBaseUrl',
    'https://docs.google.com/forms/d/e/1FAIpQLScNcr1xZ9YHULag7rdS5-LUU_e1G1XS5kfDI85T10RVTAeZXA/viewform?entry.960989731=',
    'RANGER_CLUBHOUSE_REVIEW_FORM_URL'
);
configureString(
    'ManualReviewGoogleSheetId',
    '1T6ZoSHoQhjlcqOs0J-CQ7HoMDQd_RGs4y_SQSvOb15M',
    'RANGER_CLUBHOUSE_REVIEW_SHEET_ID'
);

// Google Sheets Credentials
configureString('ManualReviewAuthConfig', '', 'RANGER_CLUBHOUSE_REVIEW_AUTH_CONFIG');


// Salesforce sandbox (sbx) parameters
// Note you must set SFsbxPassword in local.config.php!
configureString(
    'SFsbxClientId',
    '3MVG9zZht._ZaMunO578Jy7KHxp2oy5KMiCnRBHoLOUKvk6hUB3jHcmgsnMImbo30PPXFL4p5qUSYrMdYQ_C5',
    'RANGER_CLUBHOUSE_SALESFORCE_SBX_CLIENT_ID'
);
configureString(
    'SFsbxClientSecret',
    '90600550470841821',
    'RANGER_CLUBHOUSE_SALESFORCE_SBX_CLIENT_SECRET'
);
configureString(
    'SFsbxUsername',
    'philapi@burningman.com.dev3',
    'RANGER_CLUBHOUSE_SALESFORCE_SBX_USER'
);
configureString(
    'SFsbxAuthUrl',
    'https://test.salesforce.com/services/oauth2/token',
    'RANGER_CLUBHOUSE_SALESFORCE_SBX_AUTH_URL'
);
configureString(
    'SFsbxPassword',
    '*',
    'RANGER_CLUBHOUSE_SALESFORCE_SBX_PASSWORD'
);

// Salesforce production (prd) parameters
// Note you must set SFprdPassword in local.config.php!
configureString(
    'SFprdClientId',
    '3MVG9rFJvQRVOvk6W.N0QISwohURI.shVyYx2vyhMlZ_39Wi9wohZYuDbY5Fuhd_0sOCRB1.jn.ijRia1F0Cd',
    'RANGER_CLUBHOUSE_SALESFORCE_PRD_PASSWORD'
);
configureString(
    'SFprdClientSecret',
    '7358471550048400762',
    'RANGER_CLUBHOUSE_SALESFORCE_PRD_CLIENT_SECRET'
);
configureString(
    'SFprdUsername',
    'diverdave@burningman.com',
    'RANGER_CLUBHOUSE_SALESFORCE_PRD_USER'
);
configureString(
    'SFprdAuthUrl',
    'https://login.salesforce.com/services/oauth2/token',
    'RANGER_CLUBHOUSE_SALESFORCE_PRD_AUTH_URL'
);
configureString(
    'SFprdPassword',
    '*',
    'RANGER_CLUBHOUSE_SALESFORCE_PRD_PASSWORD'
);
configureBoolean(
    'SFEnableWritebacks',
    FALSE,
    'RANGER_CLUBHOUSE_SALESFORCE_ENABLE_WRITEBACKS'
);

// Tickets, Vehicle Passes, Work Access Passes
configureBoolean(
    'TicketsAndStuffEnable',
    FALSE,
    'RANGER_CLUBHOUSE_TAS_ENABLE'
);  // Menu item
configureBoolean(
    'TicketsAndStuffEnablePNV',
    FALSE,
    'RANGER_CLUBHOUSE_TAS_ENABLE_PNV'
);  // Menu item for prospectives and alphas
configureString(
    'TAS_SubmitDate',
    '2017-07-16 23:59:00',
    'RANGER_CLUBHOUSE_TAS_SUBMIT_DATE'
);
configureString(
    'TAS_Tickets',
    'accept',
    'RANGER_CLUBHOUSE_TAS_TICKETS'
);  // Or 'accept' or 'frozen' or 'none'
configureString(
    'TAS_VP',
    'accept',
    'RANGER_CLUBHOUSE_TAS_VP'
);  // Or 'accept' or 'frozen' or 'none'
configureString(
    'TAS_WAP',
    'accept',
    'RANGER_CLUBHOUSE_TAS_WAP'
);  // Or 'accept' or 'frozen' or 'none'
configureString(
    'TAS_WAPSO',
    'accept',
    'RANGER_CLUBHOUSE_TAS_WAP_SO'
);  // Or 'accept' or 'frozen' or 'none'
configureInteger(
    'TAS_WAPSOMax',
    3,
    'RANGER_CLUBHOUSE_TAS_WAP_SO_MAX'
);  // Max # of SO WAPs
configureString(
    'TAS_BoxOfficeOpenDate',
    '2017-08-22 12:00:00',
    'RANGER_CLUBHOUSE_TAS_WAP_BOXOFFICE_OPEN_DATE'
);
configureString(
    'TAS_DefaultWAPDate',
    '2017-08-24',
    'RANGER_CLUBHOUSE_TAS_WAP_DEFAULT_DATE'
);
configureString(
    'TAS_DefaultAlphaWAPDate',
    '2017-08-25',
    'RANGER_CLUBHOUSE_TAS_WAP_DEFAULT_ALPHA_DATE'
);
configureString(
    'TAS_DefaultSOWAPDate',
    '2017-08-24',
    'RANGER_CLUBHOUSE_TAS_WAP_DEFAULT_SO_DATE'
);
configureString(
    'TAS_Email',
    'ranger-ticketing-stuff@burningman.org',
    'RANGER_CLUBHOUSE_TAS_EMAIL'
);
configureString(
    'TAS_Ticket_FAQ',
    'https://docs.google.com/document/d/1TILtNyPUygjVk9T0B7FEobwAwKOBTub-YewyMtJaIFs/edit',
    'RANGER_CLUBHOUSE_TAS_FAQ_URL'
);
configureString(
    'TAS_WAP_FAQ',
    'https://docs.google.com/document/d/1wuucvq017bQHP7-0uH2KlSWSaYW7CSvNN7siU11Ah7k/edit',
    'RANGER_CLUBHOUSE_TAS_WAP_FAQ_URL'
);
configureString(
    'TAS_VP_FAQ',
    'https://docs.google.com/document/d/1KPBD_qdyBkdDnlaVBTAVX8-U3WWcSXa-4_Kf48PbOCM/edit',
    'RANGER_CLUBHOUSE_TAS_WAP_VP_URL'
);
configureString(
    'TAS_Alpha_FAQ',
    'https://docs.google.com/document/d/1yyIAUqP4OdjGTZeOqy1PxE_1Hynhkh0cFzALQkTM-ds/edit',
    'RANGER_CLUBHOUSE_TAS_ALPHA_FAQ_URL'
);

// Meal date info (needs to change every year)
configureString(
    'MealDates',
    'Pre Event is Tue 8/8 (dinner) - Sun 8/27; During Event is Mon 8/28 - Mon 9/4; Post Event is Tue 9/5 - Sat 9/9 (lunch)',
    'RANGER_CLUBHOUSE_MEAL_DATES'
);

configureBoolean('EnableEARequest', FALSE, 'RANGER_CLUBHOUSE_WAP_REQUEST_ENABLE');
configureString('ExtendedFieldZone0Title', 'Introductory Information'  );
configureString('ExtendedFieldZone2Title', 'Post-Timesheet Information');
configureString('ExtendedFieldZone3Title', 'Post-Schedule Information' );
configureString('ExtendedFieldZone4Title', 'Post-Asset Information'    );
configureString('ExtendedFieldZone7Title', 'Additional Information'    );

configureString('MysqlServer'  , 'localhost', 'RANGER_DB_HOST_NAME'    );
configureString('MysqlUsername', 'rangers'  , 'RANGER_DB_USER_NAME'    );
configureString('MysqlPassword', 'donothing', 'RANGER_DB_PASSWORD'     );
configureString('MysqlDatabase', 'rangers'  , 'RANGER_DB_DATABASE_NAME');
configureString(
    'SiteNotice',
    'Copyright 2008-2017 black rock city, llc. All information contained within this website is strictly confidential.',
    'RANGER_COPYRIGHT_NOTICE'
);

configureString ('SiteTitle',                'Black Rock Rangers Secret Clubhouse');
configureString ('AdminEmail',               'ranger-tech-ninjas@burningman.org', 'RANGER_CLUBHOUSE_EMAIL_ADMIN');
configureString ('GeneralSupportEmail',      'rangers@burningman.org', 'RANGER_CLUBHOUSE_EMAIL_SUPPORT');
configureString ('SignupUrl',                'http://jousting-at-windmills.org/clubhouse/');
configureBoolean('SendWelcomeEmail',         FALSE, 'RANGER_CLUBHOUSE_SEND_WELCOME_EMAIL');
configureString ('SqlDateFormatLiteral',     "'%Y-%m-%d'");
configureString ('SqlDateTimeFormatLiteral', "'%a %b %d @ %H:%i'");
configureString ('SqlTimeFormatLiteral',     "'%H:%i'");
configureString ('TimeZone',                 'America/Los_Angeles', 'RANGER_CLUBHOUSE_TIMEZONE');

// Optional ticket credit warning messages.
// If any are not set, no message will be displayed
configureInteger('RpTicketThreshold',   19, 'RANGER_CLUBHOUSE_THRESHOLD_RPT' );  // Ticket threshold for reduced price
configureInteger('ScTicketThreshold',   38, 'RANGER_CLUBHOUSE_THRESHOLD_CRED');  // Ticket threshold for staff credential
configureInteger('YrTicketThreshold', 2018, 'RANGER_CLUBHOUSE_THRESHOLD_YEAR');  // Ticket threshold year

// Development flags
configureBoolean('DevShowSql', false);  // if true - Show all SQL requests on the developr console or web log.
configureBoolean('DevLogSql', false);    // if true log all SQL statements to the log table
