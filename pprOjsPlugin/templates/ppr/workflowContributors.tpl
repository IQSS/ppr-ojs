<!--  Contributors -->
{capture assign=contributorsComponentdUrl}{url router=$smarty.const.ROUTE_COMPONENT component='pprPlugin.services.PPRAuthorGridHandler' op="fetchGrid" submissionId=$submission->getId() publicationId=$submission->getData('currentPublicationId') escape=false}{/capture}
{load_url_in_div id="pprContributorsContainer" url=$contributorsComponentdUrl}