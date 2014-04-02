[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
<script type="text/javascript">
<!--
function _groupExp(el) {
    var _cur = el.parentNode;

    if (_cur.className == "exp") _cur.className = "";
      else _cur.className = "exp";
}
//-->
</script>

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]
<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]    
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="ego_scheduler">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="actshop" value="[{$oViewConf->getActiveShopId()}]">
    <input type="hidden" name="updatenav" value="">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>
<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="ego_scheduler">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="trigger" value="">
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="actshop" value="[{$oViewConf->getActiveShopId()}]">
    <input type="hidden" name="language" value="[{ $actlang }]">

    [{if $output}]
        [{if $output.success }]
            <p style="color:green">[{$output.message}]</p>
            <p style="color:grey">Task in [{$output.runtime}]s ausgeführt</p>
        [{else}]
            <p style="color:red">[{$output.message}]</p>
        [{/if}]
    [{/if}]

    <h1>
        Scheduler
        [{if $oView->isSchedulerRunning()}] ist gesperrt[{/if}]
    </h1>
    [{foreach from=$oView->getTasks() item=task}]
    <div class="groupExp">
        <div>
            <a href="#" onclick="_groupExp(this);return false;" class="rc"><b>[{$task.description}]</b></a>
            <dl>
                <dd>
                    <table>
                        <tr>
                            <td>
                                [{ oxmultilang ident="EGO_SCHEDULER_ACTIVE" }]
                            </td>
                            <td>
                                <input type="hidden" name="editval[[{$task.id}]][active]" value="0" >
                                <input type="checkbox" name="editval[[{$task.id}]][active]" value="1" [{if $task.active}] checked [{/if}]> <br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                [{ oxmultilang ident="EGO_SCHEDULER_DESC" }]
                            </td>
                            <td>
                                <input type="text" name="editval[[{$task.id}]][description]" value="[{$task.description}]"> <br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                [{ oxmultilang ident="EGO_SCHEDULER_CLASS" }]
                            </td>
                            <td>
                                <input type="text" name="editval[[{$task.id}]][class]" value="[{$task.class}]"> <br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                [{ oxmultilang ident="EGO_SCHEDULER_PATH" }]
                            </td>
                            <td>
                                <input type="text" name="editval[[{$task.id}]][path]" value="[{$task.path}]"> <br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                [{ oxmultilang ident="EGO_SCHEDULER_TIMEINTERVAL" }]
                            </td>
                            <td>
                                <input type="text" name="editval[[{$task.id}]][timeinterval]" value="[{$task.timeinterval}]"> <br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                [{ oxmultilang ident="EGO_SCHEDULER_LASTLOG" }]
                            </td>
                            <td>
                                [{assign var=log value=$task.log}]
                                [{if $log.status == 0 }][{ oxmultilang ident="EGO_SCHEDULER_LOG_ERROR" }]
                                [{elseif $log.status == 1}][{ oxmultilang ident="EGO_SCHEDULER_LOG_SUCCESS" }]
                                [{elseif $log.status == 2}][{ oxmultilang ident="EGO_SCHEDULER_LOG_STARTED" }]
                                [{/if}]
                                <br />
                                [{ oxmultilang ident="EGO_SCHEDULER_LOG_MESSAGE" }]:&nbsp;
                                [{$log.message}]<br />
                                [{ oxmultilang ident="EGO_SCHEDULER_LOG_TIME" }]:&nbsp;
                                [{$log.time}]&nbsp;
                                [{ oxmultilang ident="EGO_SCHEDULER_LOG_RUNTIME" }]:&nbsp;
                                [{$log.runtime}]
                            </td>
                        </tr>
                    </table>
                    <input type="submit" name="editval[[{$task.id}]][trigger]" value="[{ oxmultilang ident="EGO_SCHEDULER_TRIGGER" }]" onclick="Javascript:document.myedit.fnc.value='trigger';document.myedit.trigger.value=[{$task.id}]" [{ $readonly }]>
                </dd>
            </dl>
        </div>
    </div>
    [{/foreach}]
    <div class="groupExp">
        <div>
            <a href="#" onclick="_groupExp(this);return false;" class="rc"><b>[{ oxmultilang ident="EGO_SCHEDULER_NEW" }]</b></a>
            <dl>
                <dd>
                    <table>
                        <tr>
                            <td>
                                [{ oxmultilang ident="EGO_SCHEDULER_ACTIVE" }]
                            </td>
                            <td>
                                <input type="checkbox" name="editval[new][active]" value=""> <br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                [{ oxmultilang ident="EGO_SCHEDULER_DESC" }]
                            </td>
                            <td>
                                <input type="text" name="editval[new][description]" value=""> <br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                [{ oxmultilang ident="EGO_SCHEDULER_CLASS" }]
                            </td>
                            <td>
                                <input type="text" name="editval[new][class]" value=""> <br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                [{ oxmultilang ident="EGO_SCHEDULER_PATH" }]
                            </td>
                            <td>
                                <input type="text" name="editval[new][path]" value=""> <br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                [{ oxmultilang ident="EGO_SCHEDULER_TIMEINTERVAL" }]
                            </td>
                            <td>
                                <input type="text" name="editval[new][timeinterval]" value=""> <br />
                            </td>
                        </tr>
                    </table>
                </dd>
            </dl>
        </div>
    </div>
    <br>
    <input type="submit" class="edittext" id="oLockButton" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onclick="Javascript:document.myedit.fnc.value='save'" [{ $readonly }]>
    <br>
    <input type="submit" class="edittext" id="oUnLockButton" value="[{ oxmultilang ident="EGO_SCHEDULER_UNLOCK" }]" onclick="Javascript:document.myedit.fnc.value='unlockScheduler'" [{ $readonly }]>
    <br>

</form>
[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]

