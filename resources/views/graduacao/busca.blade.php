@extends('adminlte::page')

@section('title', config('app.name') . ' - Buscar aluno')

@section('content_header')
    <h1>Buscar aluno</h1>
@stop

@can('secretaria')

@section('content')

	@include('flash')

    <div class="box box-primary">
        <form id="busca" role="form" method="post" action"/buscaReplicado">
            {{ csrf_field() }} 
            <div class="box-body">
                <div class="form-group">
                    <label for="codpes">Nº USP</label>
                    <input type="text" class="form-control" id="codpes" name="codpes" placeholder="Nº USP" pattern="\d+" required>
                </div>
                <div class="form-group">
                    <label for="parteNome">Nome</label>
                    <input type="text" class="form-control" id="parteNome" name="parteNome" placeholder="Parte do Nome">
                    <table class="table table-striped table-hover"><tbody id="alunos"></tbody></table>
                </div>
            </div>
            <div class="box-footer">
                <button type="button" class="btn btn-primary" onclick="document.getElementById('busca').submit();">Buscar</button>
            </div>
        </form>
    </div>

    @if (isset($dadosAcademicos))

    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab_1" data-toggle="tab">Aluno</a></li>
            <li><a href="#tab_2" data-toggle="tab">Créditos</a></li>
            <li><a href="#tab_3" data-toggle="tab">Faltam</a></li>
			<li><a href="#tab_4" data-toggle="tab">Eletivas disponíveis</a></li>
			<li><a href="#tab_5" data-toggle="tab"><span class="fa fa-fw fa-file-pdf-o"></span>PDF</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
          		<div class="box-primary">
            		<div class="box-header">
						
						@if( config('ccg.wsFoto') === true  )
                        <img style="margin-left: 0px; margin-bottom: 10px;" class="profile-user-img img-responsive img-circle" 
                            src="data: image/jpeg; base64, {{ Uspdev\Wsfoto::obter($dadosAcademicos->codpes) }}" alt="{{ $dadosAcademicos->nompes }}" />
              			@endif
              			
              			<h3 class="box-title">{{ $dadosAcademicos->codpes }} - {{ $dadosAcademicos->nompes }}</h3>
            		</div>
            		<div class="box-body table-responsive no-padding">
              			<table class="table table-hover" style="width: 50%;">
                			<tr>
                  				<th>Curso</th>
                  				<td>{{ $dadosAcademicos->codcur }} - {{ $dadosAcademicos->nomcur }}</td>
                			</tr>
                			<tr>
                  				<th>Habilitação</td>
                  				<td>{{ $dadosAcademicos->codhab }} - {{ $dadosAcademicos->nomhab }}</td>
                			</tr>
                			<tr>
                  				<th>Ano de ingresso</td>
                  				<td>{{ Carbon\Carbon::parse($dadosAcademicos->dtainivin)->format('Y') }}</td>
                			</tr>
                			<tr>
                  				<th>Programa</td>
                  				<td>{{ $dadosAcademicos->codpgm }}</td>
                			</tr>
                			@if (isset($curriculoAluno->id_crl))
							<tr>
                  				<th colspan="2">
									<table class="table table-hover">
										<thead>
											<tr>
												<th>&nbsp;</th>
												<th>Eletivas</th>
												<th>Livres</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>Créditos-aula necessários</td>
												<td>{{ $curriculoAluno->numcredisoptelt }}</td>
												<td>{{ $curriculoAluno->numcredisoptliv }}</td>
											</tr>	
											<tr>
												<td>Créditos-aula cursados</td>
												<td>{{ $numcredisoptelt }}</td>
												<td>{{ $numcredisoptliv }}</td>
											</tr>
											<tr>
												<td>Créditos-aula a concluir</td>
												<td>
													{{ (($curriculoAluno->numcredisoptelt - $numcredisoptelt) < 0) ? 0 : $curriculoAluno->numcredisoptelt - $numcredisoptelt }}
												</td>
												<td>
													{{ (($curriculoAluno->numcredisoptliv - $numcredisoptliv) < 0) ? 0 : $curriculoAluno->numcredisoptliv - $numcredisoptliv }}
												</td>
											</tr>																																		
										</tbody>
									</table>
								</td>
                			</tr>
							@endif															
              			</table>
            		</div>
          		</div>
            </div>
              
            <div class="tab-pane" id="tab_2">
          		<div class="box-primary">
            		<div class="box-header">
              			<h3 class="box-title">{{ $dadosAcademicos->codpes }} - {{ $dadosAcademicos->nompes }}</h3> 
            		</div>
            		<div class="box-body table-responsive">
						<h4>Disciplinas Concluídas</h4> 
						<table style="width: 100%;" class="table table-bordered table-striped table-hover" id="disciplinasObrigatorias">
							<thead>
								<tr>
									<th><label>Diciplinas Obrigatórias</label></th>
									<th>&nbsp;</th>
								</tr>                     
								<tr>
									<th>Disciplinas</th>
									<th>Créditos/Aula</th>
								</tr>                                          
							</thead>
							<tbody>                                                  
								@foreach ($disciplinasObrigatoriasConcluidas as $disciplinaConcluida)                  
									@if ( (!in_array($disciplinaConcluida, $disciplinasOptativasLivresConcluidas)) and 
										(!in_array($disciplinaConcluida, $disciplinasOptativasEletivasConcluidas)) )
										<tr>
											<td style="width: 70%;">{{ $disciplinaConcluida }} - 
												{{ Uspdev\Replicado\Graduacao::nomeDisciplina($disciplinaConcluida) }}</td>
											<td style="width: 30%;">{{ Uspdev\Replicado\Graduacao::creditosDisciplina($disciplinaConcluida) }}</td>
										</tr>
									@endif
								@endforeach
							</tbody>					
							<tfoot>
								<tr>
									<th style="text-align: right;">Total de créditos</th>
									<th></th>
								</tr>
							</tfoot>							
						</table>
						<br />
						<form id="dispensas" role="form" method="post" action="/dispensas">
						{{ csrf_field() }} 
						<table style="width: 100%;" class="table table-bordered table-striped table-hover" id="disciplinasOptativasEletivas">
							<thead>
								<tr>
									<th><label>Disciplinas Optativas Eletivas</label></th>
									<th>&nbsp;</th>
								</tr>                     
								<tr>
									<th>Dispensa&nbsp;&nbsp;|&nbsp;&nbsp;Disciplinas</th>
									<th>Créditos/Aula</th>
								</tr>                                          
							</thead>
							<tbody>                                                     
								@foreach ($disciplinasConcluidas as $disciplinaConcluida)                  
									@if (in_array($disciplinaConcluida['coddis'], $disciplinasOptativasEletivasConcluidas))
										<tr>
											<td style="width: 70%;" 
												id="td{{ $disciplinaConcluida['coddis'] }}">
												
												@php 
													if (in_array($disciplinaConcluida['coddis'], $dispensas)) {
														$checked = 'checked';
														$creaul = 0;
													} else {
														$checked = '';
														$creaul = $disciplinaConcluida['creaul'];
													}	
												@endphp
												
												<input type="checkbox" name="coddis[]" id="input{{ $disciplinaConcluida['coddis'] }}" 
													title="Marque para dispensar o aluno desta disciplina" value="{{ $disciplinaConcluida['coddis'] }}"
													{{ $checked }}>
												&nbsp;&nbsp;{{ $disciplinaConcluida['coddis'] }} - 
												{{ Uspdev\Replicado\Graduacao::nomeDisciplina($disciplinaConcluida['coddis']) }}</td>
											<td style="width: 30%;">{{ $creaul }}</td>
										</tr>
									@endif
								@endforeach
							</tbody>					
							<tfoot>
								<tr>
									<th style="text-align: right;">
										<input type="hidden" class="form-control" id="id_crl" name="id_crl" value="{{ $curriculoAluno->id_crl }}">
										<input type="hidden" class="form-control" id="codpes" name="codpes" value="{{ $dadosAcademicos->codpes }}">
										<button type="button" class="btn btn-primary"
											onclick="document.getElementById('dispensas').submit();"
											title="Recalcular os créditos considerando as dispensas">Recalcular</button>
										&nbsp;&nbsp;Total de créditos
									</th>
									<th></th>
								</tr>
							</tfoot>
						</table>
						</form>
						<br />
						<table style="width: 100%;" class="table table-bordered table-striped table-hover" id="disciplinasLicenciaturas">
							<thead>
								<tr>
									<th><label>Disciplinas Licenciaturas</label></th>
									<th>&nbsp;</th>
								</tr>                     
								<tr>
									<th>Disciplinas</th>
									<th>Créditos/Aula</th>
								</tr>                                          
							</thead>
							<tbody>                                                     
								@foreach ($disciplinasLicenciaturasConcluidas as $disciplinaConcluida)                  
									@if (in_array($disciplinaConcluida, $disciplinasLicenciaturasConcluidas))
										<tr>
											<td style="width: 70%;">{{ $disciplinaConcluida }} - 
												{{ Uspdev\Replicado\Graduacao::nomeDisciplina($disciplinaConcluida) }}</td>
											<td style="width: 30%;">{{ Uspdev\Replicado\Graduacao::creditosDisciplina($disciplinaConcluida) }}</td>
										</tr>
									@endif
								@endforeach
							</tbody>
							<tfoot>
								<tr>
									<th style="text-align: right;">Total de créditos</th>
									<th></th>
								</tr>
							</tfoot>
						</table>												
						<br />
						<form id="dispensasLivres" role="form" method="post" action="/dispensas/livres">
						{{ csrf_field() }} 
						<table style="width: 100%;" class="table table-bordered table-striped table-hover" id="disciplinasOptativasLivres">
							<thead>
								<tr>
									<th><label>Disciplinas Optativas Livres</label></th>
									<th>&nbsp;</th>
								</tr>                     
								<tr>
									<th>Dispensa&nbsp;&nbsp;|&nbsp;&nbsp;Disciplinas</th>
									<th>Créditos/Aula</th>
								</tr>                                          
							</thead>
							<tbody>                                                     
								@foreach ($disciplinasConcluidas as $disciplinaConcluida)                  
									@if (in_array($disciplinaConcluida['coddis'], $disciplinasOptativasLivresConcluidas))
										@if (App\Ccg\Aluno::getConcluiuEquivalente($disciplinaConcluida['coddis'], $curriculoAluno->id_crl, 'Obrigatoria') == 0 and
											App\Ccg\Aluno::getConcluiuEquivalente($disciplinaConcluida['coddis'], $curriculoAluno->id_crl, 'Licenciatura') == 0
										)
											<tr>
												<td style="width: 70%;" id="td{{ $disciplinaConcluida['coddis'] }}">
													
													@php 
														if (in_array($disciplinaConcluida['coddis'], $dispensas)) {
															$checked = 'checked';
															$creaul = 0;
														} else {
															$checked = '';
															$creaul = $disciplinaConcluida['creaul'];
														}	
													@endphp
													
													<input type="checkbox" name="coddis[]" id="input{{ $disciplinaConcluida['coddis'] }}" 
														title="Marque para dispensar o aluno desta disciplina" value="{{ $disciplinaConcluida['coddis'] }}"
														{{ $checked }}>
													&nbsp;&nbsp;{{ $disciplinaConcluida['coddis'] }} - 
													{{ Uspdev\Replicado\Graduacao::nomeDisciplina($disciplinaConcluida['coddis']) }}</td>
												<td style="width: 30%;">{{ $creaul }}</td>
											</tr>
										@endif
									@endif
								@endforeach
							</tbody>
							<tfoot>
								<tr>
									<th style="text-align: right;">
										<input type="hidden" class="form-control" id="id_crl" name="id_crl" value="{{ $curriculoAluno->id_crl }}">
										<input type="hidden" class="form-control" id="codpes" name="codpes" value="{{ $dadosAcademicos->codpes }}">
										<button type="button" class="btn btn-primary"
											onclick="document.getElementById('dispensasLivres').submit();"
											title="Recalcular os créditos considerando as dispensas">Recalcular</button>
										&nbsp;&nbsp;Total de créditos</th>
									<th></th>
								</tr>
							</tfoot>
						</table>
						</form>
            		</div>					
          		</div>
            </div>
              
			<div class="tab-pane" id="tab_3">
          		<div class="box-primary">
            		<div class="box-header">
						<h3 class="box-title">{{ $dadosAcademicos->codpes }} - {{ $dadosAcademicos->nompes }}</h3>
            		</div>
            		<div class="box-body table-responsive">
						<h4>Disciplinas que Faltam</h4>
						<table style="width: 100%;" class="table table-bordered table-striped table-hover datatable">
							<thead>
								<tr>
									<th><label>Disciplinas Obrigatórias a concluir</label></th>
								</tr>                     
								<tr>
									<th>Disciplinas</th>
								</tr>                                          
							</thead>
							<tbody>                                                     
								@foreach ($disciplinasObrigatoriasFaltam as $disciplinaObrigatoriaFalta)                  
									<tr>
										<td style="width: 100%;">{{ $disciplinaObrigatoriaFalta }} - 
											{{ Uspdev\Replicado\Graduacao::nomeDisciplina($disciplinaObrigatoriaFalta) }}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						<br />
						<table style="width: 100%;" class="table table-bordered table-striped table-hover datatable">
							<thead>
								<tr>
									<th><label>Disciplinas Licenciaturas a concluir</label></th>
								</tr>                     
								<tr>
									<th>Disciplinas</th>
								</tr>                                          
							</thead>
							<tbody>                                                     
								@foreach ($disciplinasLicenciaturasFaltam as $disciplinaLicenciaturaFalta)                  
									<tr>
										<td style="width: 100%;">{{ $disciplinaLicenciaturaFalta }} - 
											{{ Uspdev\Replicado\Graduacao::nomeDisciplina($disciplinaLicenciaturaFalta) }}</td>
									</tr>
								@endforeach
							</tbody>
						</table>																		
            		</div>					
          		</div>
            </div>

			<div class="tab-pane" id="tab_4">
          		<div class="box-primary">
            		<div class="box-header">
						<h3 class="box-title">{{ $dadosAcademicos->codpes }} - {{ $dadosAcademicos->nompes }}</h3>
            		</div>
            		<div class="box-body table-responsive">
						<h4>Disciplinas Optativas Eletivas disponíveis</h4>
						<table style="width: 100%;" class="table table-bordered table-striped table-hover datatable">
							<thead>                   
								<tr>
									<th>Disciplinas</th>
									<th>Créditos/Aula</th>									
								</tr>                                          
							</thead>
							<tbody>                                                     
								@foreach ($disciplinasOptativasEletivasFaltam as $disciplinaOptativaEletivaFalta)                  
									<tr>
										<td style="width: 100%;">{{ $disciplinaOptativaEletivaFalta }} - 
											{{ Uspdev\Replicado\Graduacao::nomeDisciplina($disciplinaOptativaEletivaFalta) }}</td>
										<td>
											{{ Uspdev\Replicado\Graduacao::creditosDisciplina($disciplinaOptativaEletivaFalta) }}</td>											
									</tr>
								@endforeach
							</tbody>
						</table>																		
            		</div>					
          		</div>
            </div>

			<div class="tab-pane" id="tab_5">
          		<div class="box-primary">
            		<div class="box-header">
						<h3 class="box-title">{{ $dadosAcademicos->codpes }} - {{ $dadosAcademicos->nompes }}</h3>
            		</div>
            		<div class="box-body table-responsive">
						<form id="observacoes" role="form" method="POST" action="/creditos/{{ $dadosAcademicos->codpes }}">
							{{ csrf_field() }}
							<div class="form-group">
								<label>Observações</label>
							@if (isset(App\AlunosObservacoes::where(['id_crl' => $curriculoAluno->id_crl, 'codpes' => $dadosAcademicos->codpes])->first()->txtobs))	
								<textarea id="txtobs" name="txtobs" class="form-control" rows="3" 
									placeholder="Digite aqui">{{ App\AlunosObservacoes::where([
											'id_crl' => $curriculoAluno->id_crl, 
											'codpes' => $dadosAcademicos->codpes
										])->first()->txtobs }}</textarea>
							@else
								<textarea id="txtobs" name="txtobs" class="form-control" rows="3" maxlength="500" 
									placeholder="Digite aqui"></textarea>
							@endif
								<input type="hidden" class="form-control" id="id_crl" name="id_crl" value="{{ $curriculoAluno->id_crl }}">
								<input type="hidden" class="form-control" id="codpes" name="codpes" value="{{ $dadosAcademicos->codpes }}">
							</div>
							<button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('observacoes').submit();">
								Gerar PDF <span class="fa fa-fw fa-file-pdf-o"></span></button>
						</form>
            		</div>					
          		</div>
            </div>			

        </div>
    </div>

    @endif

@stop

@section('js')

    <script type="text/javascript">
        $(document).ready(function() {      
            //Initialize Select2 Elements
            $('.select2').select2({
                placeholder:    "Selecione",
                allowClear:     true
            });
            
            //Datepicker
            $('.datepicker').datepicker({
                format:         "dd/mm/yyyy",
                viewMode:       "years", 
                minViewMode:    "years",
                autoclose:      true
            });

            // DataTables
            $('.datatable').DataTable({
                language    	: {
                    url     : '//cdn.datatables.net/plug-ins/1.10.19/i18n/Portuguese-Brasil.json'
                },  
                paging      	: true,
                lengthChange	: true,
                searching   	: true,
                ordering    	: true,
                info        	: true,
                autoWidth   	: true,
                lengthMenu		: [
					[ 10, 25, 50, 100, -1 ],
					[ '10 linhas', '25 linhas', '50 linhas', '100 linhas', 'Mostar todos' ]
    			],
				pageLength  	: -1
            });

			// Total de créditos
			$('#disciplinasObrigatorias').dataTable( {
				"footerCallback" : function(tfoot, data, start, end, display){
					var api = this.api();
					$(api.column(1).footer()).html(
						api.column(1).data().reduce(function(a, b){
							a = parseInt(a);
							b = parseInt(b);
							return a + b;
						}, 0)
					);
				},
				language    	: {
                    url     : '//cdn.datatables.net/plug-ins/1.10.19/i18n/Portuguese-Brasil.json'
                },
                lengthMenu		: [
					[ 10, 25, 50, 100, -1 ],
					[ '10 linhas', '25 linhas', '50 linhas', '100 linhas', 'Mostar todos' ]
    			],				  
				pageLength  	: -1
			});
			
			$('#disciplinasOptativasLivres').dataTable( {
				"footerCallback" : function(tfoot, data, start, end, display){
					var api = this.api();
					$(api.column(1).footer()).html(
						api.column(1).data().reduce(function(a, b){
							a = parseInt(a);
							b = parseInt(b);
							return a + b;
						}, 0)
					);
				},
				language    	: {
                    url     : '//cdn.datatables.net/plug-ins/1.10.19/i18n/Portuguese-Brasil.json'
                }, 
                lengthMenu		: [
					[ 10, 25, 50, 100, -1 ],
					[ '10 linhas', '25 linhas', '50 linhas', '100 linhas', 'Mostar todos' ]
    			],				 
				pageLength  	: -1
			});			

			$('#disciplinasOptativasEletivas').dataTable( {
				"footerCallback" : function(tfoot, data, start, end, display){
					var api = this.api();
					$(api.column(1).footer()).html(
						api.column(1).data().reduce(function(a, b){
							a = parseInt(a);
							b = parseInt(b);
							return a + b;
						}, 0)
					);
				},
				language    	: {
                    url     : '//cdn.datatables.net/plug-ins/1.10.19/i18n/Portuguese-Brasil.json'
                },  
                lengthMenu		: [
					[ 10, 25, 50, 100, -1 ],
					[ '10 linhas', '25 linhas', '50 linhas', '100 linhas', 'Mostar todos' ]
    			],				
				pageLength  	: -1
			});				
			
			$('#disciplinasLicenciaturas').dataTable( {
				"footerCallback" : function(tfoot, data, start, end, display){
					var api = this.api();
					$(api.column(1).footer()).html(
						api.column(1).data().reduce(function(a, b){
							a = parseInt(a);
							b = parseInt(b);
							return a + b;
						}, 0)
					);
				},
				language    	: {
                    url     : '//cdn.datatables.net/plug-ins/1.10.19/i18n/Portuguese-Brasil.json'
                }, 
				lengthMenu		: [
					[ 10, 25, 50, 100, -1 ],
					[ '10 linhas', '25 linhas', '50 linhas', '100 linhas', 'Mostar todos' ]
    			], 
				pageLength  	: -1
			});				

			// Enter desativado 			
			// $(window).keydown(function(event){
			// 	if((event.keyCode == 13)) {
			// 		event.preventDefault();
			// 		return false;
			// 	}
			// });
        });
        
        $('#parteNome').on('keypress', function() {
            if ($('#parteNome').val().length >= {{ config('ccg.parteNomeLength') }} ) {
                $.get("busca/" + $('#parteNome').val(), function(data) {
                	$('#alunos').empty();
                	$.each(data, function(i, value) {
                    	var tr = $("<tr title='Clique para ver as informações de " + value.nompes + "' onclick=$('#codpes').val(" + value.codpes + ");$('#alunos').empty();$('#busca').submit(); />");
                        	tr.append($("<td/>", {
                            	text : value.nompes
                        	}))
                    	$('#alunos').append(tr);
                	});
            	}); 
            }
        });
    </script>

@stop

@endcan