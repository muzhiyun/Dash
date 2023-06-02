<!DOCTYPE html>
<html ng-app="DashPlayer" lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Dash JavaScript Player</title>
    <meta name="description" content=""/>
    <link rel="icon" type="image/x-icon" href="https://dashif.org/img/favicon.ico"/>
    <meta name="viewport" content="width=device-width, height=device-height, user-scalable=no">

    <link rel="stylesheet" href="app/lib/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="app/lib/bootstrap/css/bootstrap-theme.css">
    <link rel="stylesheet" href="app/lib/bootstrap/css/bootstrap-glyphicons.css">
    <link rel="stylesheet" href="app/css/main.css">
    <link rel="stylesheet" href="../../contrib/akamai/controlbar/controlbar.css">

    <!--Libs-->
    <script src="app/lib/jquery/jquery-3.1.1.min.js"></script>

    <script src="app/lib/angular/angular.min.js"></script>
    <script src="app/lib/angular/angular-resource.min.js"></script>
    <script src="app/lib/angular/angular-flot.js"></script>

    <script src="app/lib/bootstrap/js/bootstrap.min.js"></script>

    <script src="app/lib/flot/jquery.flot.min.js"></script>
    <script src="app/lib/flot/jquery.flot.resize.min.js"></script>
    <script src="app/lib/flot/jquery.flot.axislabels.js"></script>

    <!-- App -->
    <script src="../../dist/dash.all.debug.js"></script>
    <script src="../../dist/dash.mss.debug.js"></script>
    <script src="../../contrib/akamai/controlbar/ControlBar.js"></script>
    <script src="app/src/cast.js"></script>
    <script src="app/main.js"></script>
    <script src="app/rules/DownloadRatioRule.js"></script>
    <script src="app/rules/ThroughputRule.js"></script>

    <!-- Google Cast -->
    <script src="https://www.gstatic.com/cv/js/sender/v1/cast_sender.js?loadCastFramework=1"></script>

</head>
<body ng-controller="DashController">

<!-- Mobile Stream Menu-->
<div class="modal fade" id="streamModal">
    <div class="modal-dialog">
        <div class="list-group modal-list">
            <ul>
                <li ng-repeat="item in availableStreams" ng-class="{'sub':item.submenu}">
                    <span ng-show="!item.submenu" ng-click="setStream(item)">{{item.name}}</span>
                    <span ng-show="item.submenu">{{item.name}}</span>
                    <ul ng-show="item.submenu">
                        <li ng-repeat="subitem in item.submenu">
                            <span ng-click="setStream(subitem)" ng-if="subitem.url"
                                  data-dismiss="modal">{{subitem.name}}</span>
                            <span ng-if="!subitem.url">{{subitem.name}}</span>
                            <ul ng-if="subitem.submenu">
                                <li ng-repeat="subsubitem in subitem.submenu">
                                    <span ng-click="setStream(subsubitem)"
                                          data-dismiss="modal">{{subsubitem.name}}</span>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>


<!-- TITLE BAR -->
<div class="container">
    <div class="row title-header">

        <div class="branding">
            <a href="http://dashif.org/" target="_blank"><img class="image" src="app/img/if.png"/></a>
            <span id="big-title">Reference Client</span>
            <span ng-bind="version"></span>
            <span id="commit-info"><!-- commit-info --></span>
        </div>

        <!-- Using iframe to solve pre-flight request issue from GIT-->
        <div class="top-buttons">
            <iframe id="star-button"
                    src="//ghbtns.com/github-btn.html?user=Dash-Industry-Forum&repo=dash.js&type=watch&count=true&size=large"
                    height="30" width="150">
            </iframe>
            <iframe id="fork-button"
                    src="//ghbtns.com/github-btn.html?user=Dash-Industry-Forum&repo=dash.js&type=fork&count=true&size=large"
                    height="30" width="150">
            </iframe>
        </div>
    </div>
    <div class="row">
        <div class="input-group">
            <div id="desktop-streams" class="input-group-btn">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                    Stream <span class="caret"></span>
                </button>
                <ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
                    <li class="dropdown-submenu" ng-if="item.submenu" ng-repeat="item in availableStreams"
                        ng-mouseover="onStreamItemHover(item)">
                        <a tabindex="-1" href="#">{{item.name}}</a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-submenu" ng-repeat="subitem in item.submenu" ng-if="subitem.submenu">
                                <a tabindex="-1" href="#">{{subitem.name}}</a>
                                <ul class="dropdown-menu">
                                    <li ng-repeat="subsubitem in subitem.submenu">
                                        <a title="{{ subsubitem.moreInfo && 'See ' + subsubitem.moreInfo + ' for more information' || undefined }}"
                                           ng-click="setStream(subsubitem)">{{subsubitem.name}}</a>
                                    </li>
                                </ul>
                            </li>
                            <li ng-repeat="subitem in item.submenu" ng-if="subitem.url">
                                <a title="{{ subitem.moreInfo && 'See ' + subitem.moreInfo + ' for more information' || undefined }}"
                                   ng-click="setStream(subitem)">{{subitem.name}}</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div id="mobile-streams" class="input-group-btn">
                <button type="button" class="btn btn-primary" data-toggle="modal" href="#streamModal">
                    Stream <span class="caret"></span>
                </button>
            </div>
            <input type="text" class="form-control" ng-model="selectedItem.url">
            <span class="input-group-btn">
                    <button class="btn btn-default" ng-click="toggleOptionsGutter(!optionsGutter)"
                            ng-cloak>{{getOptionsButtonLabel()}}</button>
                    <button class="btn btn-default" type="button" ng-click="doStop()">Stop</button>
                    <button class="btn btn-primary" type="button" ng-click="doLoad()">Load</button>
                <!-- Testbutton -->
                    <button class="btn btn-default" type="button" ng-click="copyQueryUrl(); copyNotificationShow()">Copy Settings URL</button>
                </span>
        </div>
        <div id='copyNotificationPopup' class='copyPopup'>
            <div id='copyPopupContent' class='copyPopupContent'> URL Copied!</div>
        </div>
    </div>

    <!-- OPTIONS MENU-->
    <div ng-cloak class="row options-wrapper" ng-class="{'options-show':optionsGutter, 'options-hide':!optionsGutter}">
        <div class="options-item">
            <div class="options-item-title">Playback</div>
            <div class="options-item-body">
                <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                       title="Enables automatic startup of the media once the media is loaded">
                    <input type="checkbox" ng-model="autoPlaySelected" ng-change="toggleAutoPlay()"
                           ng-checked="autoPlaySelected">
                    Auto-Play
                </label>
                <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                       title="Enables looping of the media once playback has completed">
                    <input type="checkbox" id="loop-cb" ng-model="loopSelected" ng-checked="loopSelected">
                    Loop
                </label>
                <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                       title="Enables scheduling fragments whiled paused increasing the buffer.">
                    <input type="checkbox" ng-model="scheduleWhilePausedSelected"
                           ng-change="toggleScheduleWhilePaused()" ng-checked="scheduleWhilePausedSelected">
                    Schedule While Paused
                </label>
                <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                       title="Enables calculation of the DVR window based on the timestamps in SegmentTimeline element.">
                    <input type="checkbox" ng-model="calcSegmentAvailabilityRangeFromTimelineSelected"
                           ng-change="toggleCalcSegmentAvailabilityRangeFromTimeline()"
                           ng-checked="calcSegmentAvailabilityRangeFromTimelineSelected">
                    Calculate segment availability from timeline
                </label>
                <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                       title="Enable reuse of existing MediaSource Sourcebuffers during period transition.">
                    <input type="checkbox" ng-model="reuseExistingSourceBuffersSelected"
                           ng-change="toggleReuseExistingSourceBuffers()"
                           ng-checked="reuseExistingSourceBuffersSelected">
                    Reuse SourceBuffers
                </label>
                <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                       title="Enables local storage of player state (last bitrate, a/v or text track etc). This is then used when the next time media is played.">
                    <input type="checkbox" id="localStorageCB" ng-model="localStorageSelected"
                           ng-change="toggleLocalStorage()" ng-checked="localStorageSelected">
                    Allow Local Storage
                </label>
                <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                       title="Enables jump small gaps (discontinuities) in the media streams">
                    <input type="checkbox" id="jumpGapsCB" ng-model="jumpGapsSelected" ng-change="toggleJumpGaps()"
                           ng-checked="jumpGapsSelected">
                    Jump Small Gaps
                </label>
                <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                       title="Enable catchup mode for non low latency streams">
                    <input type="checkbox" id="liveCatchupCB" ng-model="liveCatchupEnabled"
                           ng-change="toggleLiveCatchupEnabled()" ng-checked="liveCatchupEnabled">
                    Live catchup
                </label>
                <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                       title="Use the ContentSteering information from the MPD if enabled">
                    <input type="checkbox" id="applyContentSteering" ng-model="applyContentSteering"
                           ng-change="toggleApplyContentSteering()" ng-checked="applyContentSteering">
                   Apply ContentSteering
                </label>
                <div class="sub-options-item">
                    <div class="sub-options-item-title">Catchup mechanism</div>
                    <div class="sub-options-item-body">
                        <label data-toggle="tooltip" data-placement="right"
                               title="Default catchup mechanism to keep the desired live latency.">
                            <input type="radio" id="liveCatchupModeDefault" autocomplete="off"
                                   ng-model="liveCatchupMode"
                                   name="catchupMode" value="liveCatchupModeDefault"
                                   ng-click="changeLiveCatchupMode('liveCatchupModeDefault')">
                            Default
                        </label>
                        <label data-toggle="tooltip" data-placement="right"
                               title="LoL+ based catchup mechanism to keep the desired live latency.">
                            <input type="radio" id="liveCatchupModeLoLP" autocomplete="off" ng-model="liveCatchupMode"
                                   name="catchupMode"
                                   ng-click="changeLiveCatchupMode('liveCatchupModeLoLP')" value="liveCatchupModeLoLP">
                            LoL+ based
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="options-item">
            <div class="options-item-title">ABR Options</div>
            <div class="options-item-body">
                <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                       title="Enables faster ABR switching (time to render). Only when the new quality is higher than the current.">
                    <input type="checkbox" id="fastSwitchCB" ng-model="fastSwitchSelected"
                           ng-change="toggleFastSwitch()" ng-checked="fastSwitchSelected">
                    Fast Switching ABR
                </label>
                <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                       title="Enables auto switch video quality using selected ABR strategy">
                    <input type="checkbox" id="videoAutoSwitchCB" ng-model="videoAutoSwitchSelected"
                           ng-change="toggleVideoAutoSwitch()" ng-checked="videoAutoSwitchSelected">
                    Video Auto Switch
                </label>
                <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                       title="Forces quality switch to be immediatly effective">
                    <input type="checkbox" id="forceQualitySwitchCB" ng-model="forceQualitySwitchSelected"
                           ng-change="toggleForceQualitySwitch()" ng-checked="forceQualitySwitchSelected">
                    Force Quality Switch
                </label>
                <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                       title="ABR - Use custom ABR rules">
                    <input type="checkbox" id="customABRRules" ng-model="customABRRulesSelected"
                           ng-change="toggleUseCustomABRRules()" ng-checked="customABRRulesSelected">
                    Use Custom ABR Rules
                </label>
                <div class="sub-options-item">
                    <div class="sub-options-item-title">Standard</div>
                    <div class="sub-options-item-body">
                        <label data-toggle="tooltip" data-placement="right"
                               title="Dynamically switch between BOLA and Throughput strategies.">
                            <input type="radio" id="abrDynamic" autocomplete="off" name="abrStrategy" checked="checked"
                                   ng-model="ABRStrategy"
                                   ng-click="changeABRStrategy('abrDynamic')" value="abrDynamic">
                            ABR Strategy: Dynamic
                        </label>
                        <label data-toggle="tooltip" data-placement="right"
                               title="Choose bitrate based on buffer level.">
                            <input type="radio" id="abrBola" autocomplete="off" name="abrStrategy"
                                   ng-model="ABRStrategy"
                                   ng-click="changeABRStrategy('abrBola')" value="abrBola">
                            ABR Strategy: BOLA
                        </label>
                        <label data-toggle="tooltip" data-placement="right"
                               title="Choose bitrate based on recent throughput.">
                            <input type="radio" id="abrThroughput" autocomplete="off" name="abrStrategy"
                                   ng-model="ABRStrategy"
                                   ng-click="changeABRStrategy('abrThroughput')" value="abrThroughput">
                            ABR Strategy: Throughput
                        </label>
                        <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                               title="Enable/Disable InsufficientBufferRule">
                            <input type="checkbox" ng-change="toggleBufferRule()" id="insufficientBufferRule"
                                   ng-model="additionalAbrRules.insufficientBufferRule">
                            InsufficientBufferRule
                        </label>
                        <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                               title="Enable/Disable SwitchHistoryRule">
                            <input type="checkbox" ng-change="toggleBufferRule()" id="switchHistoryRule"
                                   ng-model="additionalAbrRules.switchHistoryRule">
                            SwitchHistoryRule
                        </label>
                        <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                               title="Enable/Disable DroppedFramesRule">
                            <input type="checkbox" ng-change="toggleBufferRule()" id="droppedFramesRule"
                                   ng-model="additionalAbrRules.droppedFramesRule">
                            DroppedFramesRule
                        </label>
                        <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                               title="Enable/Disable AbandonRequestsRule">
                            <input type="checkbox" ng-change="toggleBufferRule()" id="abandonRequestsRule"
                                   ng-model="additionalAbrRules.abandonRequestsRule">
                            AbandonRequestsRule
                        </label>
                    </div>
                </div>

                <div class="sub-options-item">
                    <div class="sub-options-item-title">Low-Latency</div>
                    <div class="sub-options-item-body">
                        <label data-toggle="tooltip" data-placement="right"
                               title="Choose bitrate using the L2A algorithm.">
                            <input type="radio" id="abrL2all" autocomplete="off" name="abrStrategy"
                                   ng-model="ABRStrategy"
                                   ng-click="changeABRStrategy('abrL2A')" value="abrL2A">
                            ABR Strategy: L2A-LL
                        </label>
                        <label data-toggle="tooltip" data-placement="right"
                               title="Choose bitrate using the LoL+ algorithm.">
                            <input type="radio" id="abrLoLP" autocomplete="off" name="abrStrategy"
                                   ng-model="ABRStrategy"
                                   ng-click="changeABRStrategy('abrLoLP')" value="abrLoLP">
                            ABR Strategy: LoL+
                        </label>
                    </div>
                </div>
                <div class="sub-options-item">
                    <div class="sub-options-item-title">LL throughput calculation</div>
                    <div class="sub-options-item-body">
                        <label data-toggle="tooltip" data-placement="right"
                               title="Default fetch throughput calculation for low latency streaming based on downloaded data chunks.">
                            <input type="radio" id="abrFetchThroughputCalculationDownloadedData" autocomplete="off"
                                   ng-model="abrThroughputCalculationMode"
                                   name="abrFetchThroughputCalculation"
                                   value="abrFetchThroughputCalculationDownloadedData"
                                   ng-click="changeFetchThroughputCalculation('abrFetchThroughputCalculationDownloadedData')">
                            Data chunks
                        </label>
                        <label data-toggle="tooltip" data-placement="right"
                               title="LoL+ fetch throughput calculation for low latency streaming based on moof parsing.">
                            <input type="radio" id="abrFetchThroughputCalculationMoofParsing" autocomplete="off"
                                   ng-model="abrThroughputCalculationMode"
                                   checked="checked" name="abrFetchThroughputCalculation"
                                   value="abrFetchThroughputCalculationMoofParsing"
                                   ng-click="changeFetchThroughputCalculation('abrFetchThroughputCalculationMoofParsing')">
                            moof parsing
                        </label>
                        <label data-toggle="tooltip" data-placement="right"
                               title="Further approach for measurement and estimation (FAME) for fetch throughput calculation for low latency streaming based on AAST decisioning / availability of chunk and optimistic estimation.">
                            <input type="radio" id="abrFetchThroughputCalculationAAST" autocomplete="off"
                                   ng-model="abrThroughputCalculationMode"
                                   name="abrFetchThroughputCalculation" value="abrFetchThroughputCalculationAAST"
                                   ng-click="changeFetchThroughputCalculation('abrFetchThroughputCalculationAAST')">
                            AST decisioning
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="options-item">
            <div class="options-item-title">DRM Options</div>
            <div class="options-item-body">
                <form>
                    <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                           title="">
                        <input type="checkbox" ng-model="drmPlayready.isActive">
                        Playready
                    </label>

                    <div id="drmPlayreadyLicenseForm" ng-show="drmPlayready.isActive">
                        <label class="options-label">License URL:</label>
                        <input type="text" class="form-control" placeholder="" ng-model="drmPlayready.licenseServerUrl">

                        <!-- Open Header Dialogue Window -->
                        <button id="playreadyRequestHeaderDialogueBtn" class="btn btn-primary"
                                ng-click="openDialogue('playready')">
                            Add Request-Headers
                        </button>

                        <!-- Header Dialogue Window Content -->
                        <div id="playreadyRequestHeaderDialogue" class="requestHeaderDialogue">
                            <div class="requestHeaderDialogueContent">
                                <button class="close" ng-click="closeDialogue('playready')">&times;</button>
                                <button class="btn btn-primary" ng-click="addPopupInput('playready')">Add additional
                                    Header
                                </button>

                                <div ng-repeat="header in playreadyRequestHeaders track by $index"
                                     ng-value="{{$index}}">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-6 col-lg-offset-3">
                                            <label class="options-label">Request Header Key: </label>
                                            <input type="text" class="form-control" placeholder=""
                                                   ng-model="header.key" name="playreadyKey_{{header.id}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-lg-6 col-lg-offset-3">
                                            <label class="options-label">Request Header Value: </label>
                                            <input type="text" class="form-control" placeholder=""
                                                   ng-model="header.value" name="playreadyValue_{{header.id}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-lg-2 col-lg-offset-3">
                                            <button class="btn btn-danger"
                                                    ng-click="removePopupInput('playready', $index)">
                                                Remove Header
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div ng-show="prioritiesEnabled">
                            <label class="options-label">Priority</label>
                            <select name="playreadyPriority" id="playreadyPriority" ng-model="drmPlayready.priority"
                                    ng-init="drmPlayready.priority='1'">
                                <option value=0>0</option>
                                <option value=1>1</option>
                                <option value=2>2</option>
                            </select>
                        </div>
                    </div>

                    <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                           title="">
                        <input type="checkbox" ng-model="drmWidevine.isActive">
                        Widevine
                    </label>

                    <div id="drmWidevineLicenseForm" ng-show="drmWidevine.isActive">
                        <label class="options-label">License URL:</label>
                        <input type="text" class="form-control" placeholder="" ng-model="drmWidevine.licenseServerUrl">

                        <!-- Open Header Dialogue Window -->
                        <button id="widevineRequestHeaderDialogueBtn" class="btn btn-primary"
                                ng-click="openDialogue('widevine')">
                            Add Request-Headers
                        </button>

                        <!-- Header Dialogue Window Content -->
                        <div id="widevineRequestHeaderDialogue" class="requestHeaderDialogue">
                            <div class="requestHeaderDialogueContent">
                                <button class="close" ng-click="closeDialogue('widevine')">&times;</button>
                                <button class="btn btn-primary" ng-click="addPopupInput('widevine')">Add additional
                                    Header
                                </button>

                                <div ng-repeat="header in widevineRequestHeaders track by $index"
                                     ng-value="{{$index}}">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-6 col-lg-offset-3">
                                            <label class="options-label">Request Header Key: </label>
                                            <input type="text" class="form-control" placeholder=""
                                                   ng-model="header.key" name="widevineKey_{{header.id}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-lg-6 col-lg-offset-3">
                                            <label class="options-label">Request Header Value: </label>
                                            <input type="text" class="form-control" placeholder=""
                                                   ng-model="header.value" name="widevineValue_{{header.id}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-lg-2 col-lg-offset-3">
                                            <button class="btn btn-danger"
                                                    ng-click="removePopupInput('widevine', $index)">
                                                Remove Header
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div ng-show="prioritiesEnabled">
                            <label class="options-label">Priority</label>
                            <select name="widevinePriority" id="widevinePriority" ng-model="drmWidevine.priority"
                                    ng-init="drmWidevine.priority='0'">
                                <option value=0>0</option>
                                <option value=1>1</option>
                                <option value=2>2</option>
                            </select>
                        </div>
                    </div>

                    <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                           title="">
                        <input type="checkbox" ng-model="drmClearkey.isActive">
                        Clearkey
                    </label>

                    <div id="drmClearkeyLicenseForm" ng-show="drmClearkey.isActive">
                        <div id="drmClearkeyKidKeyMode" ng-show="drmClearkey.inputMode === 'kidKey'">
                            <label class="options-label">Kid:</label>
                            <input type="text" class="form-control" placeholder="" ng-model="drmClearkey.kid">
                            <label class="options-label">Key:</label>
                            <input type="text" class="form-control" placeholder="" ng-model="drmClearkey.key">
                        </div>
                        <div id="drmClearkeyLicenseserverMode" ng-show="drmClearkey.inputMode === 'licenseServer'">
                            <label class="options-label">License URL:</label>
                            <input type="text" class="form-control" placeholder=""
                                   ng-model="drmClearkey.licenseServerUrl">
                        </div>

                        <!-- Open KID=KEY Dialogue Window -->
                        <button id="additionalClearkeysDialogueBtn" class="btn btn-primary dialogue-btn"
                                ng-click="openDialogue('additionalClearkeys')">
                            Add additional clearkeys
                        </button>

                        <!-- KID=KEY Dialogue Window Content -->
                        <div id="additionalClearkeysDialogue" class="requestHeaderDialogue">
                            <div class="requestHeaderDialogueContent">
                                <button class="close" ng-click="closeDialogue('additionalClearkeys')">&times;</button>
                                <button class="btn btn-primary" ng-click="addPopupInput('additionalClearkeys')">Add
                                    KID=KEY pair
                                </button>

                                <div ng-repeat="clearkey in additionalClearkeyPairs track by $index"
                                     ng-value="{{$index}}">

                                    <div class="row">
                                        <div class="col-md-12 col-lg-6 col-lg-offset-3">
                                            <label class="options-label">KID: </label>
                                            <input type="text" class="form-control" placeholder=""
                                                   ng-model="clearkey.kid" name="clearkeyKID_{{header.id}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-lg-6 col-lg-offset-3">
                                            <label class="options-label">KEY: </label>
                                            <input type="text" class="form-control" placeholder=""
                                                   ng-model="clearkey.key" name="clearkeyKEY_{{header.id}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-lg-2 col-lg-offset-3">
                                            <button class="btn btn-danger"
                                                    ng-click="removePopupInput('additionalClearkeys', $index)">Remove
                                                KID=KEY
                                                pair
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Open Header Dialogue Window -->
                        <button id="clearkeyRequestHeaderDialogueBtn" class="btn btn-primary"
                                ng-click="openDialogue('clearkey')">
                            Add Request-Headers
                        </button>

                        <!-- Header Dialogue Window Content -->
                        <div id="clearkeyRequestHeaderDialogue" class="requestHeaderDialogue">
                            <div class="requestHeaderDialogueContent">
                                <button class="close" ng-click="closeDialogue('clearkey')">&times;</button>
                                <button class="btn btn-primary" ng-click="addPopupInput('clearkey')">Add additional
                                    Header
                                </button>

                                <div ng-repeat="header in clearkeyRequestHeaders track by $index"
                                     ng-value="{{$index}}">

                                    <div class="row">
                                        <div class="col-md-12 col-lg-6 col-lg-offset-3">
                                            <label class="options-label">Request Header Key: </label>
                                            <input type="text" class="form-control" placeholder=""
                                                   ng-model="header.key" name="clearkeyKey_{{header.id}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-lg-6 col-lg-offset-3">
                                            <label class="options-label">Request Header Value: </label>
                                            <input type="text" class="form-control" placeholder=""
                                                   ng-model="header.value" name="clearkeyValue_{{header.id}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-lg-2 col-lg-offset-3">
                                            <button class="btn btn-danger"
                                                    ng-click="removePopupInput('clearkey', $index)">
                                                Remove Header
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div ng-show="prioritiesEnabled">
                            <label class="options-label">Priority</label>
                            <select name="clearkeyPriority" id="clearkeyPriority" ng-model="drmClearkey.priority"
                                    ng-init="drmClearkey.priority='2'">
                                <option value=0>0</option>
                                <option value=1>1</option>
                                <option value=2>2</option>
                            </select>
                        </div>
                    </div>
                </form>
                <div class="sub-options-item">
                    <div class="sub-options-item-title">Priorities</div>
                    <div class="sub-options-item-body">
                        <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                               title="Enable DRM-Priorisation">
                            <input type="checkbox" ng-model="prioritiesEnabled">
                            DRM Priorisation
                        </label>
                    </div>
                </div>
                <div class="sub-options-item">
                    <div class="sub-options-item-title">Clearkey Options</div>
                    <div class="sub-options-item-body">
                        <label data-toggle="tooltip" data-placement="right"
                               title="Use KID=Key to specify Clearkey DRM.">
                            <input type="radio" id="kidKey" autocomplete="off" name="inputMode"
                                   ng-model="drmClearkey.inputMode" value="kidKey">
                            KID=KEY
                        </label>
                        <label data-toggle="tooltip" data-placement="right"
                               title="Use Licenseserver-URL to specify Clearkey DRM">
                            <input type="radio" id="licenseServer" autocomplete="off" name="inputMode"
                                   ng-model="drmClearkey.inputMode" value="licenseServer">
                            License Server
                        </label>
                    </div>
                </div>
                <div class="sub-options-item">
                    <div class="sub-options-item-title">License server</div>
                    <div class="sub-options-item-body">
                        <label data-toggle="tooltip" data-placement="right"
                               title="DRM Today">
                            <input type="checkbox" id="drmTodayServer" ng-model="drmToday" autocomplete="off"
                                   name="inputMode">
                            DRM Today
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="options-item">
            <div class="options-item-title">Live delay</div>
            <div class="options-item-body">
                <label class="options-label">Initial Live delay:</label>
                <input type="text" class="form-control" placeholder="value seconds" ng-model="initialLiveDelay"
                       ng-change="updateInitialLiveDelay()">
                <label class="options-label">Initial LiveDelayFragmentCount:</label>
                <input type="text" class="form-control" placeholder="value number" ng-model="liveDelayFragmentCount"
                       ng-change="updateLiveDelayFragmentCount()">
                <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                       title="Use latency targets defined in ServiceDescription elements">
                    <input type="checkbox" id="applyServiceDescription" ng-model="applyServiceDescription"
                           ng-change="toggleApplyServiceDescription()" ng-checked="applyServiceDescription">
                    Apply ServiceDescription
                </label>
                <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                       title="Overwrite the default live delay and honor the SuggestedPresentationDelay attribute in by the manifest">
                    <input type="checkbox" id="useSuggestedPresentationDelay" ng-model="useSuggestedPresentationDelay"
                           ng-change="toggleUseSuggestedPresentationDelay()" ng-checked="useSuggestedPresentationDelay">
                    Use SuggestedPresentationDelay
                </label>
            </div>
        </div>
        <div class="options-item">
            <div class="options-item-title">Initial Settings</div>
            <div class="options-item-body">
                <label class="options-label">Initial bitrate Video:</label>
                <input type="text" class="form-control" placeholder="value in kbps" ng-model="initialVideoBitrate"
                       ng-change="updateInitialBitrateVideo()">
                <label class="options-label">Minimum bitrate Video:</label>
                <input type="text" class="form-control" placeholder="value in kbps" ng-model="minVideoBitrate"
                       ng-change="updateMinimumBitrateVideo()">
                <label class="options-label">Maximum bitrate Video:</label>
                <input type="text" class="form-control" placeholder="value in kbps" ng-model="maxVideoBitrate"
                       ng-change="updateMaximumBitrateVideo()">
                <label class="options-label">Audio:</label>
                <input type="text" class="form-control" placeholder="audio initial lang, e.g. 'en'"
                       ng-model="initialSettings.audio" ng-change="updateInitialLanguageAudio()">
                <label class="options-label">Video:</label>
                <input type="text" class="form-control" placeholder="initial role, e.g. 'alternate'"
                       ng-model="initialSettings.video" ng-change="updateInitialRoleVideo()">
                <label class="options-label">Text:</label>
                <input type="text" class="form-control" placeholder="text initial lang, e.g. 'en'"
                       ng-model="initialSettings.text" ng-change="updateInitialLanguageText()">
                <input type="text" class="form-control" placeholder="text initial role, e.g. 'caption'"
                       ng-model="initialSettings.textRole" ng-change="updateInitialRoleText()">
                <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                       title="Enable subtitle on loading text">
                    <input type="checkbox" id="enableTextAtLoading" ng-model="initialSettings.textEnabled"
                           ng-change="toggleText()">
                    Enable Text At Loading
                </label>
                <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                       title="Force text streaming">
                    <input type="checkbox" id="enableForceTextStreaming" ng-model="initialSettings.forceTextStreaming"
                           ng-change="toggleForcedTextStreaming()">
                    Force Text Streaming
                </label>

            </div>
        </div>
        <div class="options-item">
            <div class="options-item-title">Track Switch Mode</div>
            <div class="options-item-body">
                <label class="options-label">Audio:</label>
                <label data-toggle="tooltip" data-placement="right"
                       title="When a track is switched, the portion of the buffer that contains old track data is cleared">
                    <input type="radio" id="always-replace-audio" autocomplete="off" name="track-switch-audio"
                           ng-model='audioTrackSwitchMode'
                           checked="checked" ng-click="changeTrackSwitchMode('alwaysReplace', 'audio')"
                           value="alwaysReplace">
                    always replace
                </label>
                <label data-toggle="tooltip" data-placement="right"
                       title="When a track is switched, the portion of the buffer that contains old track data is NOT cleared">
                    <input type="radio" id="never-replace-audio" autocomplete="off" name="track-switch-audio"
                           ng-model="audioTrackSwitchMode"
                           ng-click="changeTrackSwitchMode('neverReplace', 'audio')" value="neverReplace">
                    never replace
                </label>
                <label class="options-label">Video:</label>
                <label data-toggle="tooltip" data-placement="right"
                       title="When a track is switched, the portion of the buffer that contains old track data is cleared">
                    <input type="radio" id="always-replace-video" autocomplete="off" name="track-switch-video"
                           ng-model="videoTrackSwitchMode"
                           checked="checked" ng-click="changeTrackSwitchMode('alwaysReplace', 'video')"
                           value="alwaysReplace">
                    always replace
                </label>
                <label data-toggle="tooltip" data-placement="right"
                       title="When a track is switched, the portion of the buffer that contains old track data is NOT cleared">
                    <input type="radio" id="never-replace-video" autocomplete="off" name="track-switch-video"
                           ng-model="videoTrackSwitchMode"
                           ng-click="changeTrackSwitchMode('neverReplace', 'video')" value="neverReplace">
                    never replace
                </label>
            </div>
        </div>
        <div class="options-item">
            <div class="options-item-title">Debug</div>
            <div class="options-item-body">
                <label class="options-label">Log Level:</label>
                <label data-toggle="tooltip" data-placement="right"
                       title="Don't write any log message in browser console">
                    <input type="radio" id="log-none" value="none" autocomplete="off" name="log-level"
                           ng-model="currentLogLevel"
                           ng-click="setLogLevel()">
                    NONE
                </label>
                <label data-toggle="tooltip" data-placement="right"
                       title="Sets log level of to LOG_LEVEL_FATAL">
                    <input type="radio" id="log-fatal" value="fatal" autocomplete="off" name="log-level"
                           ng-model="currentLogLevel"
                           ng-click="setLogLevel()">
                    FATAL
                </label>
                <label data-toggle="tooltip" data-placement="right"
                       title="Sets log level of to LOG_LEVEL_ERROR">
                    <input type="radio" id="log-error" value="error" autocomplete="off" name="log-level"
                           ng-model="currentLogLevel"
                           ng-click="setLogLevel()">
                    ERROR
                </label>
                <label data-toggle="tooltip" data-placement="right"
                       title="Sets log level of to LOG_LEVEL_WARNING">
                    <input type="radio" id="log-warning" value="warning" autocomplete="off" name="log-level"
                           ng-model="currentLogLevel"
                           ng-click="setLogLevel()">
                    WARNING
                </label>
                <label data-toggle="tooltip" data-placement="right"
                       title="Sets log level of to LOG_LEVEL_INFO">
                    <input type="radio" id="log-info" value="info" autocomplete="off" name="log-level" checked="checked"
                           ng-model="currentLogLevel"
                           ng-click="setLogLevel()">
                    INFO
                </label>
                <label data-toggle="tooltip" data-placement="right"
                       title="Sets log level of to LOG_LEVEL_DEBUG">
                    <input type="radio" id="log-debug" value="debug" autocomplete="off" name="log-level"
                           ng-model="currentLogLevel"
                           ng-click="setLogLevel()">
                    DEBUG
                </label>
            </div>
        </div>
        <div class="options-item">
            <div class="options-item-title">CMCD</div>
            <div class="options-item-body">
                <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                       title="Enables reporting of CMCD parameters via query parameters">
                    <input type="checkbox" ng-model="cmcdEnabled" ng-change="toggleCmcdEnabled()"
                           ng-checked="cmcdEnabled">
                    Enable CMCD Reporting
                </label>
                <label class="options-label">Session ID:</label>
                <input type="text" class="form-control" placeholder="mandatory session id" ng-model="cmcdSessionId"
                       ng-change="updateCmcdSessionId()">
                <label class="options-label">Content ID:</label>
                <input type="text" class="form-control" placeholder="content id" ng-model="cmcdContentId"
                       ng-change="updateCmcdContentId()">
                <label class="options-label" data-toggle="tooltip" data-placement="right"
                       title="A static value to be used as RTP parameter">Requested maximum throughput (rtp):</label>
                <input type="text" class="form-control" placeholder="rtp in kbps" ng-model="cmcdRtp"
                       ng-change="updateCmcdRtp()">
                <label class="options-label" data-toggle="tooltip" data-placement="right"
                       title="This value is used as a factor for the rtp value calculation: rtp = minBandwidth * rtpSafetyFactor. If not specified this value defaults to 5. Note that this value is only used when no static rtp value is defined.">RTP
                    safety factor:</label>
                <input type="text" class="form-control" placeholder="Default 5" ng-model="cmcdRtpSafetyFactor"
                       ng-change="updateCmcdRtpSafetyFactor()">
                <label class="options-label" title="Specifies the CMCD data transition mode">Transition Mode:</label>
                <label data-toggle="tooltip" data-placement="right"
                       title="Send the CMCD metrics via query parameter">
                    <input type="radio" id="cmcd-query" value="query" checked="checked" autocomplete="off"
                           ng-model="cmcdMode"
                           name="cmcd-mode"
                           ng-click="setCmcdMode()">
                    Query
                </label>
                <label data-toggle="tooltip" data-placement="right"
                       title="Send the CMCD metrics via custom HTTP header">
                    <input type="radio" id="cmcd-header" value="header" autocomplete="off" name="cmcd-mode"
                           ng-model="cmcdMode"
                           ng-click="setCmcdMode()">
                    Header
                </label>
                <label class="options-label" data-toggle="tooltip" title="List of enabled cmcd keys for http headers">Enabled
                    Header Keys</label>
                <input type="text" class="form-control" placeholder="e.g. 'br,d,ot,...'" ng-model="cmcdEnabledKeys"
                       ng-change="updateCmcdEnabledKeys()">
            </div>
        </div>
        <div class="options-item">
            <div class="options-item-title">CMSD</div>
            <div class="options-item-body">
                <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                       title="Enables parsing of CMSD response header parameters">
                    <input type="checkbox" ng-model="cmsdEnabled" ng-change="toggleCmsdEnabled()"
                           ng-checked="cmsdEnabled">
                    Enable CMSD Parsing
                </label>
                <label class="topcoat-checkbox" data-toggle="tooltip" data-placement="right"
                       title="Apply maximum suggested bitrate from CMSD response headers">
                    <input type="checkbox" id="cmsdApplyMb" ng-model="cmsdApplyMb"
                           ng-change="toggleCmsdApplyMb()" ng-checked="cmsdApplyMb">
                    Apply maximum suggested bitrate
                </label>
                <label class="options-label">Etp weight ratio:</label>
                <input type="text" class="form-control" placeholder="weight ratio" ng-model="cmsdEtpWeightRatio"
                       ng-change="updateCmsdEtpWeightRatio()">
            </div>
        </div>

    </div>

    <!--VIDEO PLAYER / CONTROLS -->
    <div class="row">
        <div class="dash-video-player col-md-9">
            <div id="videoContainer" class="videoContainer">
                <video></video>
                <div id="video-caption"></div>
                <div id="cast-msg" ng-if="isCasting">
                    {{ castPlayerState === 'IDLE' ? 'Ready to cast stream' : castPlayerState }}
                </div>
                <div id="videoController" class="video-controller unselectable" ng-cloak>
                    <div id="playPauseBtn" class="btn-play-pause" data-toggle="tooltip" data-placement="bottom"
                         title="Play/Pause">
                        <span id="iconPlayPause" class="icon-play"></span>
                    </div>
                    <span id="videoTime" class="time-display">00:00:00</span>
                    <div id="fullscreenBtn" class="btn-fullscreen control-icon-layout" data-toggle="tooltip"
                         data-placement="bottom" title="Fullscreen">
                        <span class="icon-fullscreen-enter"></span>
                    </div>
                    <div id="castBtn" class="btn-cast control-icon-layout" data-toggle="tooltip" data-placement="bottom"
                         title="Cast">
                        <google-cast-launcher></google-cast-launcher>
                    </div>
                    <div id="bitrateListBtn" class="btn-bitrate control-icon-layout" data-toggle="tooltip"
                         data-placement="bottom" title="Bitrate List">
                        <span class="icon-bitrate"></span>
                    </div>
                    <input type="range" id="volumebar" class="volumebar" value="1" min="0" max="1" step=".01"/>
                    <div id="muteBtn" class="btn-mute control-icon-layout" data-toggle="tooltip" data-placement="bottom"
                         title="Mute">
                        <span id="iconMute" class="icon-mute-off"></span>
                    </div>
                    <div id="trackSwitchBtn" class="btn-track-switch control-icon-layout" data-toggle="tooltip"
                         data-placement="bottom" title="Track List">
                        <span class="icon-tracks"></span>
                    </div>
                    <div id="captionBtn" class="btn-caption control-icon-layout" data-toggle="tooltip"
                         data-placement="bottom" title="Closed Caption / Subtitles">
                        <span class="icon-caption"></span>
                    </div>
                    <span id="videoDuration" class="duration-display">00:00:00</span>
                    <div class="seekContainer">
                        <div id="seekbar" class="seekbar seekbar-complete">
                            <div id="seekbar-buffer" class="seekbar seekbar-buffer"></div>
                            <div id="seekbar-play" class="seekbar seekbar-play"></div>
                        </div>
                    </div>
                    <div id="thumbnail-container" class="thumbnail-container">
                        <div id="thumbnail-elem" class="thumbnail-elem"></div>
                        <div id="thumbnail-time-label" class="thumbnail-time-label"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STATS TAB CONTENT -->
        <div class="col-md-3 tabs-section">
            <div class="bs-callout bs-callout-danger" id="http-warning-container" style="display: none;">
                <span id="http-warning-text"></span>
            </div>
            <div class="bs-callout bs-callout-warning">
                <h5><span class="label label-warning" style="margin-right:3px">Updated</span>Export settings</h5>
                Our export settings feature creates shorter URLs now.
                Click on "Copy Settings URL" on the top right and paste the URL in the address bar of your browser. The
                current settings are compared to the default settings and the difference is stored using query parameters.
            </div>
            <div class="bs-callout bs-callout-information">
                <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                Additional samples can be found in the <a
                href="../index.html" target="_blank">Sample Section</a>.
            </div>
            <div>
                <ul class="nav nav-tabs" role="tablist">
                    <li class="active">
                        <a href="#videoStatsTab" role="tab" data-toggle="tab">
                            Video
                        </a>
                    </li>
                    <li><a href="#audioStatsTab" role="tab" data-toggle="tab">
                        Audio
                    </a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane fade active in" id="videoStatsTab">
                        <div class="panel-body panel-stats" ng-cloak>
                            <!-- VIDEO STATS ITEMS-->
                            <div class="text-success">
                                <input id="videoBufferCB" type="checkbox" ng-model="chartState.video.buffer.selected"
                                       ng-change="enableChartByName('buffer', 'video')">
                                <label class="text-primary" for="videoBufferCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="The length of the forward buffer, in seconds">Buffer Length : </label> {{videoBufferLength}}
                            </div>

                            <div class="text-success">
                                <input id="videoBitrateCB" type="checkbox" ng-model="chartState.video.bitrate.selected"
                                       ng-change="enableChartByName('bitrate', 'video')">
                                <label class="text-primary" for="videoBitrateCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="The bitrate of the representation being downloaded">Bitrate Downloading
                                    :</label> {{videoBitrate}} kbps
                            </div>

                            <div class="text-success">
                                <input id="videoPendingIndexCB" type="checkbox"
                                       ng-model="chartState.video.pendingIndex.selected"
                                       ng-change="enableChartByName('pendingIndex', 'video')">
                                <label class="text-primary" for="videoPendingIndexCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="The representation index being downloaded and appended to the buffer">Index
                                    Downloading :</label> {{videoPendingIndex}} / {{videoPendingMaxIndex}}
                            </div>
                            <div class="text-success">
                                <input id="videoIndexCB" type="checkbox" ng-model="chartState.video.index.selected"
                                       ng-change="enableChartByName('index', 'video')">
                                <label class="text-primary" for="videoIndexCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="The representation index being rendered.">Index playing
                                    :</label> {{videoIndex}} / {{videoMaxIndex}}
                            </div>
                            <div class="text-success">
                                <input id="videoDroppedFramesCB" type="checkbox"
                                       ng-model="chartState.video.droppedFPS.selected"
                                       ng-change="enableChartByName('droppedFPS', 'video')">
                                <label class="text-primary" for="videoDroppedFramesCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="The absolute count of frames dropped by the rendering pipeline since play commenced">Dropped
                                    Frames :</label> {{videoDroppedFrames}}
                            </div>
                            <div class="text-success">
                                <input id="videoLatencyCB" type="checkbox" ng-model="chartState.video.latency.selected"
                                       ng-change="enableChartByName('latency', 'video')">
                                <label class="text-primary" for="videoLatencyCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="The minimum, average and maximum latency over the last 4 requested segments. Latency is the time in seconds from request of segment to receipt of first byte">Latency
                                    (min|avg|max) :</label> {{videoLatency}}
                            </div>
                            <div class="text-success">
                                <input id="videoDownloadCB" type="checkbox"
                                       ng-model="chartState.video.download.selected"
                                       ng-change="enableChartByName('download', 'video')">
                                <label class="text-primary" for="videoDownloadCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="The minimum, average and maximum download time for the last 4 requested segments. Download time is the time in seconds from first byte being received to the last byte">Download
                                    (min|avg|max) :</label> {{videoDownload}}
                            </div>
                            <div class="text-success">
                                <input id="videoRatioCB" type="checkbox" ng-model="chartState.video.ratio.selected"
                                       ng-change="enableChartByName('ratio', 'video')">
                                <label class="text-primary" for="videoRatioCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="The minimum, average and maximum ratio of the segment playback time to total download time over the last 4 segments">Ratio
                                    (min|avg|max) :</label> {{videoRatio}}
                            </div>
                            <div class="text-success" ng-show="isCMSDEnabled()">
                                <input id="mtpCB" type="checkbox"
                                       ng-model="chartState.video.mtp.selected"
                                       ng-change="enableChartByName('mtp', 'video')">
                                <label class="text-primary" for="mtpCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="The measured (averaged) throughput computed in the ABR logic">Measured throughput:</label> {{videoMtp}} Mbps
                            </div>
                            <div class="text-success" ng-show="isCMSDEnabled()">
                                <input id="etpCB" type="checkbox"
                                       ng-model="chartState.video.etp.selected"
                                       ng-change="enableChartByName('etp', 'video')">
                                <label class="text-primary" for="etpCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="The estimated throughput return by CMSD response headers">Estimated throughput:</label> {{videoEtp}} Mbps
                            </div>
                            <div class="text-success" ng-show="isDynamic">
                                <input id="liveLatencyCB" type="checkbox"
                                       ng-model="chartState.video.liveLatency.selected"
                                       ng-change="enableChartByName('liveLatency', 'video')">
                                <label class="text-primary" for="liveLatencyCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="Difference between live time and current playback position in seconds. This latency estimate does not include the time taken by the encoder to encode the content">Live
                                    Latency:</label> {{videoLiveLatency}}
                            </div>
                            <div class="text-success" ng-show="isDynamic">
                                <input id="videoPlaybackRateCB" type="checkbox"
                                       ng-model="chartState.video.playbackRate.selected"
                                       ng-change="enableChartByName('playbackRate', 'video')">
                                <label class="text-primary" data-toggle="tooltip"
                                       data-placement="top"
                                       title="Playback rate">Playback rate:</label> {{videoPlaybackRate}}
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="audioStatsTab">
                        <div class="panel-body panel-stats">
                            <!-- AUDIO STATS ITEMS-->
                            <div class="text-success">
                                <input id="audioBufferLengthCB" type="checkbox"
                                       ng-model="chartState.audio.buffer.selected"
                                       ng-change="enableChartByName('buffer', 'audio')">
                                <label class="text-primary" for="audioBufferLengthCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="The length of the forward buffer, in seconds">Buffer Length :</label> {{audioBufferLength}}
                            </div>
                            <div class="text-success">
                                <input id="audioBitrateCB" type="checkbox" ng-model="chartState.audio.bitrate.selected"
                                       ng-change="enableChartByName('bitrate', 'audio')">
                                <label class="text-primary" for="audioBitrateCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="The bitrate of the representation being downloaded">Bitrate Downloading
                                    :</label> {{audioBitrate}} kbps
                            </div>
                            <div class="text-success">
                                <input id="audioPendingIndexCB" type="checkbox"
                                       ng-model="chartState.audio.pendingIndex.selected"
                                       ng-change="enableChartByName('pendingIndex', 'audio')">
                                <label class="text-primary" for="audioPendingIndexCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="The representation index being downloaded and appended to the buffer">Index
                                    Downloading :</label> {{audioPendingIndex}}
                            </div>
                            <div class="text-success">
                                <input id="audioIndexCB" type="checkbox" ng-model="chartState.audio.index.selected"
                                       ng-change="enableChartByName('index', 'audio')">
                                <label class="text-primary" for="audioIndexCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="The representation index being rendered.">Current Index / Max Index
                                    :</label> {{audioIndex}} / {{audioMaxIndex}}
                            </div>
                            <div class="text-success">
                                <input id="audioDroppedFramesCB" type="checkbox"
                                       ng-model="chartState.audio.droppedFPS.selected"
                                       ng-change="enableChartByName('droppedFPS', 'audio')">
                                <label class="text-primary" for="audioDroppedFramesCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="The absolute count of frames dropped by the rendering pipeline since play commenced">Dropped
                                    Frames :</label> {{audioDroppedFrames}}
                            </div>
                            <div class="text-success">
                                <input id="audioLatencyCB" type="checkbox" ng-model="chartState.audio.latency.selected"
                                       ng-change="enableChartByName('latency', 'audio')">
                                <label class="text-primary" for="audioLatencyCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="The minimum, average and maximum latency over the last 4 requested segments. Latency is the time in seconds from request of segment to receipt of first byte">Latency
                                    (min|avg|max) :</label> {{audioLatency}}
                            </div>
                            <div class="text-success">
                                <input id="audioDownloadCB" type="checkbox"
                                       ng-model="chartState.audio.download.selected"
                                       ng-change="enableChartByName('download', 'audio')">
                                <label class="text-primary" for="audioDownloadCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="The minimum, average and maximum download time for the last 4 requested segments. Download time is the time in seconds from first byte being received to the last byte">Download
                                    (min|avg|max) :</label> {{audioDownload}}
                            </div>
                            <div class="text-success">
                                <input id="audioRatioCB" type="checkbox" ng-model="chartState.audio.ratio.selected"
                                       ng-change="enableChartByName('ratio', 'audio')">
                                <label class="text-primary" for="audioRatioCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="The minimum, average and maximum ratio of the segment playback time to total download time over the last 4 segments">Ratio
                                    (min|avg|max) :</label> {{audioRatio}}
                            </div>
                            <div class="text-success" ng-show="isCMSDEnabled()">
                                <input id="mtpCB" type="checkbox"
                                       ng-model="chartState.video.mtp.selected"
                                       ng-change="enableChartByName('mtp', 'audio')">
                                <label class="text-primary" for="mtpCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="The measured (averaged) throughput computed in the ABR logic">Measured throughput:</label> {{videoMtp}} Mbps
                            </div>
                            <div class="text-success" ng-show="isCMSDEnabled()">
                                <input id="etpCB" type="checkbox"
                                       ng-model="chartState.audio.etp.selected"
                                       ng-change="enableChartByName('etp', 'audio')">
                                <label class="text-primary" for="etpCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="The estimated throughput return by CMSD response headers">Estimated throughput:</label> {{audioEtp}} Mbps
                            </div>
                            <div class="text-success" ng-show="isDynamic">
                                <input id="liveLatencyCB" type="checkbox"
                                       ng-model="chartState.audio.liveLatency.selected"
                                       ng-change="enableChartByName('liveLatency', 'audio')">
                                <label class="text-primary" for="liveLatencyCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="Number of seconds of difference between the real live and the playing live">Live
                                    Latency:</label> {{audioLiveLatency}}
                            </div>
                            <div class="text-success">
                                <input id="audioPlaybackRateCB" type="checkbox"
                                       ng-model="chartState.audio.playbackRate.selected"
                                       ng-change="enableChartByName('playbackRate', 'audio')">
                                <label class="text-primary" for="audioPlaybackRateCB" data-toggle="tooltip"
                                       data-placement="top"
                                       title="Playback Rate">Playback Rate:</label> {{audioPlaybackRate}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ERROR MODAL -->
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header errorModalHeader">
                    <h5 class="modal-title" id="errorModalLabel">Error {{errorType}}</h5>
                </div>
                <div class="modal-body">
                    {{error}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- CHARTING -->
    <div class="chart-panel">
        <div class="chart-controls">
            <div class="btn-group">
                <button id="disable-chart-btn" class="btn btn-primary" ng-click="clearChartData()" ng-cloak>Clear
                </button>
                <button id="disable-chart-btn" class="btn btn-primary" ng-click="onChartEnableButtonClick()" ng-cloak>
                    {{getChartButtonLabel()}}
                </button>
            </div>
            <div id="legend-wrapper" class="legend-row">
            </div>
        </div>
        <div id="chart-wrapper">
            <div id="chart-inventory">
                <flot dataset="chartData" options="chartOptions"></flot>
            </div>
        </div>
    </div>

    <!-- Conformance violations -->
    <div class="row">
        <div class="col-md-12 conformance-violations-panel">
            <div id="conformance-violations">
                <h4>Conformance Violations </h4>
                <ul class="list-unstyled" ng-repeat="conformanceViolation in conformanceViolations">
                    <li><span class="label label-{{conformanceViolation.level}}">{{conformanceViolation.level}}</span> :
                        {{conformanceViolation.event.message}}
                    </li>
                </ul>
            </div>
        </div>
    </div>


</div>

<!-- FOOTER -->
<div class="footer-area" ng-cloak>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3 class="footer-text">Contributors:</h3>
                <a ng-repeat="item in contributors" class="footer-text" href="{{item.link}}" target="_blank">
                    <img ng-show="hasLogo(item)" ng-src="{{item.logo}}" alt="{{item.link}}"/>
                    <span class="contributor" ng-show="!hasLogo(item)">{{item.name}}</span>
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>