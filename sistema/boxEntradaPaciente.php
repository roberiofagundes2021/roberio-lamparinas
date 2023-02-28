<div class="card">

    <div class="card-header header-elements-inline">
        <h3 class="card-title">HIPÓTESE DIAGNÓSTICA</h3>
    </div>

    <div class="card-body">

        <form id="formDieta" name="formDieta" method="post" class="form-validate-jquery">									
            <div class="row" style="margin-top: 10px">
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="justificativa">História de Entrada <span class="text-danger">*</span></label>
                        <textarea rows="5" cols="5" maxLength="1000" id="historiaEntrada" name="historiaEntrada"  class="form-control" placeholder="..." ></textarea>
                        <small class="text-muted form-text">Max. 1000 caracteres - <span class="caracteresjustificativa"></span></small>
                    </div>
                </div>
            </div>
        </form>

    </div>

    <div class="card-header header-elements-inline">
        <h3 class="card-title">DIAGNÓSTICO PRINCIPAL</h3>
    </div>

    <div class="card-body">

        <form id="formDieta" name="formDieta" method="post" class="form-validate-jquery">
            <div class="col-lg-12 mb-3 row">
                <!-- titulos -->
                <div class="col-lg-6">
                    <label>CID-10 <span class="text-danger">*</span></label>
                </div>
            
                <div class="col-lg-6">
                    <label>Procedimento <span class="text-danger">*</span></label>
                </div>
                <!-- campos -->										
                <div class="col-lg-6">
                    <select id="cid10" name="cid10" class="select-search">
                        <option value=''>Selecione</option>
                    </select>											
                </div>
                <div class="col-lg-6">
                    <select id="servico" name="servico" class="select-search">
                        <option value=''>Selecione</option>
                    </select>											
                </div>
            </div>                    
        </form>

    </div>

</div>

<div class="card " style="padding: 15px">
    <div class="col-md-12">
        <div class="row">                                    
            <div class="col-md-10" style="text-align: left;">
                <button type="button" class="btn btn-lg btn-success mr-1" id="salvarObservacaoEntrada">Salvar</button>
                <button type="button" class="btn btn-lg btn-secondary mr-1">Imprimir</button>
            </div>            
        </div>
    </div> 
</div>