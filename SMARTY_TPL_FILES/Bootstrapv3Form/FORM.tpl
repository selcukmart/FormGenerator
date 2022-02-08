{if !isset($editFormAction)}
    {$editFormAction = ''}
{/if}
{if !isset($method)}
    {$method = 'post'}
{/if}

{if !isset($method)}
    {$method = 'post'}
{/if}

{if !isset($enctype)}
    {$enctype = 'multipart/form-data'}
{/if}
<form action="{$editFormAction}" method="{$method}" enctype="{$enctype}" name="{$name}" id="{$id}">
    {$inputs}
</form>