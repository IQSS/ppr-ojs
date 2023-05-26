<!--  Contributors -->
{capture assign=contributorsComponentdUrl}{url router=$smarty.const.ROUTE_COMPONENT component='grid.users.author.AuthorGridHandler' op="fetchGrid" submissionId=$submission->getId() publicationId=$submission->getData('currentPublicationId') escape=false}{/capture}
{load_url_in_div id="pprContributorsContainer" url=$contributorsComponentdUrl}