{** TRICK TO LOAD THE OJS TEMPLATE WITH THE SAME NAME *}
{** THIS WILL ALLOW TO ADD DATA BEFORE OR AFTER A TEMPLATE WITHOUT OVERRIDING ITS CONTENTS *}
{include file="submission/form/step2.tpl.load_ojs"}

{capture assign="modalHeader"}{translate key="submission.ppr.files.validation.header"}{/capture}
{capture assign="modalDescription"}{translate key="submission.ppr.files.validation.description"}{/capture}
{capture assign="modalButton"}{translate key="submission.ppr.files.validation.button"}{/capture}
{include file="ppr/modalMessage.tpl" modalHeader=$modalHeader modalDescription=$modalDescription modalButton=$modalButton}
