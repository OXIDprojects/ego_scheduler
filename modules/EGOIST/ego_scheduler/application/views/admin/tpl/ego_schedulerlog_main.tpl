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
<style>
    .groupExp  dl dt {
        width:95px;
        margin-top:3px;
    }

    .groupExp dl dd {
        padding-left:105px;
    }
</style>

[{if $readonly}]
[{assign var="readonly" value="readonly disabled"}]
[{else}]
[{assign var="readonly" value=""}]
[{/if}]

[{cycle assign="_clear_" values=",2" }]

<div class="info">
    <strong>[{ oxmultilang ident="ego_schedulerlog_HEADLINE" }]</strong>
</div>

[{if $oView->getExceptionsFromLogfile() }]
<a href="[{ $oViewConf->getSelfLink() }]cl=ego_schedulerlog_main&fnc=archiveLogFile" style="color: red;">[{ oxmultilang ident="ego_schedulerlog_ARCHIVE" }]</a>
<br /><br />
[{/if}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="ego_schedulerlog_main">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="actshop" value="[{$oViewConf->getActiveShopId()}]">
    <input type="hidden" name="updatenav" value="">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>
[{foreach from=$oView->getExceptionsFromLogfile() item=exception}]
    [{assign var=type value=$exception.type}]
    <div class="groupExp">
        <div>
            <a href="#" onclick="_groupExp(this);return false;" class="rc">
                <b>
                    [{$exception.datetime}] ::
                    [{$type}]
                </b>
            </a>
            <dl>
                <dt>
                    [{oxmultilang ident='ego_schedulerlog_MSG'}]:
                </dt>
                <dd>
                    <pre>[{$exception.msg}]</pre>
                </dd>
                <div class="spacer"></div>
            </dl>
        </div>
    </div>
    [{/foreach}]
[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
