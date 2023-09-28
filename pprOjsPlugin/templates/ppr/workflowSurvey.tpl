
<div id="pprAuthorSurveyWrapper">
    {include file="controllers/extrasOnDemand.tpl"
    id="pprAuthorSurvey"
    widgetWrapper="#pprAuthorSurveyWrapper"
    moreDetailsText="surveys.ppr.author.showDetails"
    lessDetailsText="surveys.ppr.author.hideDetails"
    extraContent=$pprPluginSettings->authorDashboardSurveyHtml()
    }
</div>