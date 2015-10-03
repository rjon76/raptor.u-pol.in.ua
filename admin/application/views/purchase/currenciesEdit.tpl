<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Currencies</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
    
        <form method="post" action="">
            <div class="table-responsive">
                <table class="table table-hover">
                <thead>
                    <tr>
                        <th><strong>Currency:</strong></th>
                        <th class="text-center">EUR</th>
                        <th class="text-center">GBP</th>
                        <th class="text-center">JPY</th>
                        <th class="text-center">AUD</th>
                        <th class="text-center">CAD</th>
                        <th class="text-center">CNY</th>
                        <th class="text-center">NOK</th>
                        <th class="text-center">SEK</th>
                        <th class="text-center">PLN</th>
                        <th class="text-center">RUB</th>
                        <th class="text-center">CHF</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><strong>Ratio:</strong></td>
                        <td class="text-center"><input type="text" class="form-control" name="eur" style="width:80px;text-align:center;" value="{$purchase.val.eur}" /></td>
                        <td class="text-center"><input type="text" class="form-control" name="gbp" style="width:80px;text-align:center;" value="{$purchase.val.gbp}" /></td>
                        <td class="text-center"><input type="text" class="form-control" name="jpy" style="width:80px;text-align:center;" value="{$purchase.val.jpy}" /></td>
                        <td class="text-center"><input type="text" class="form-control" name="aud" style="width:80px;text-align:center;" value="{$purchase.val.aud}" /></td>
                        <td class="text-center"><input type="text" class="form-control" name="cad" style="width:80px;text-align:center;" value="{$purchase.val.cad}" /></td>
                        <td class="text-center"><input type="text" class="form-control" name="cny" style="width:80px;text-align:center;" value="{$purchase.val.cny}" /></td>
                        <td class="text-center"><input type="text" class="form-control" name="nok" style="width:80px;text-align:center;" value="{$purchase.val.nok}" /></td>
                        <td class="text-center"><input type="text" class="form-control" name="sek" style="width:80px;text-align:center;" value="{$purchase.val.sek}" /></td>
                        <td class="text-center"><input type="text" class="form-control" name="pln" style="width:80px;text-align:center;" value="{$purchase.val.pln}" /></td>
                        <td class="text-center"><input type="text" class="form-control" name="rub" style="width:80px;text-align:center;" value="{$purchase.val.rub}" /></td>
                        <td class="text-center"><input type="text" class="form-control" name="chf" style="width:80px;text-align:center;" value="{$purchase.val.chf}" /></td>
                    </tr>
                    <tr>
                        <td><strong>Recalculate:</strong></td>
                        <td class="text-center"><input type="checkbox" name="recalc[]" value="eur" /></td>
                        <td class="text-center"><input type="checkbox" name="recalc[]" value="gbp" /></td>
                        <td class="text-center"><input type="checkbox" name="recalc[]" value="jpy" /></td>
                        <td class="text-center"><input type="checkbox" name="recalc[]" value="aud" /></td>
                        <td class="text-center"><input type="checkbox" name="recalc[]" value="cad" /></td>
                        <td class="text-center"><input type="checkbox" name="recalc[]" value="cny" /></td>
                        <td class="text-center"><input type="checkbox" name="recalc[]" value="nok" /></td>
                        <td class="text-center"><input type="checkbox" name="recalc[]" value="sek" /></td>
                        <td class="text-center"><input type="checkbox" name="recalc[]" value="pln" /></td>
                        <td class="text-center"><input type="checkbox" name="recalc[]" value="rub" /></td>
                        <td class="text-center"><input type="checkbox" name="recalc[]" value="chf" /></td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="12" class="text-right">
                            <input type="submit" value="Update changes" class="btn btn-primary" />
                            <!--<input type="checkbox" name="recalc" />
                            With prices recalculation-->
                        </td>
                    </tr>
                    </tfoot>
                    
                </table>
            </div>
        </form>
    </div>
</div>