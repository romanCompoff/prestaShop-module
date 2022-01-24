<!-- Block mymodule -->
<div id="mymodule_block_home" class="block">
  <h4>{l s='Привет!' mod='my_module'} В заданном диапазоне цен в наличии имеется продуктов {$countProducts} {$notice}</h4>
  <div class="block_content">
  <ol>
    {foreach from=$products item=$product}
        <li>  
          {$product.reference} -
          {$product.price}
        </li>  
      {/foreach}

    </ol>
  </div>
</div>
<!-- /Block mymodule -->