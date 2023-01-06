<!DOCTYPE html>
<html>
	<head>
	    <meta charset="utf-8" />
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap" rel="stylesheet" />
	    <style type="text/css">
	    	@page{margin:0px;padding: 0px;}
	    	body{
	    		border-top-width: 30px;
	    		border-bottom-width: 30px;
	    		border-right-width: 20px;
	    		border-left-width: 20px;
	    		border-color: #c69a1d;
	    		border-style: solid;
				background-color: rgba(0,0,0,0.9);
				padding: 10px;
	    		color:#fff;
	    		font-size: 14px;
	    		font-weight: bold;
	    		font-family: 'Roboto', sans-serif;
	    	}
	    	.row{
	    		width: 100%;
	    	}
	    	table{width: 50%;text-align: center;border-collapse: collapse;float: left;padding:0 2%}
	    	table tr th,table tr td{border: 2px solid #c69a1d;padding:5px; }
	    	table tr th{font-size: 18px;}
	    	.ms-1{margin-left: 5px;}
	    	.mb-1{margin-bottom: 10px;}
	    	h4{margin:5px}

	    	#watermark {
	            position: fixed;
	            bottom:   20%;
	            left:     25%;

	            /** Change image dimensions**/
	            width:    12cm;
	            height:   12cm;

	            /** Your watermark should be behind every content**/
	            z-index:  -1000;
	             opacity: 0.3; 
	        }



	        #resp-table {
				width: 100%;
				display: table;
			}
			#resp-table-header{
				display: table-header-group;
				/*background-color: gray;
				font-weight: bold;*/
				font-size: 18px;
			}
			.table-header-cell{
				display: table-cell;
				text-align: justify;
				padding: 10px;
				border: 2px solid #c69a1d;
			}
			#resp-table-body{
				display: table-row-group;
			}

			.resp-table-row{
				display: table-row;
			}
			.table-body-cell{
				display: table-cell;
				text-align: center;
				vertical-align: middle;
				padding:5px;
				border: 2px solid #c69a1d;
			}
	    </style>
	</head>

	<body>
		 <div id="watermark" class="">
	        <img src="{{ public_path('img/logo.png') }}" height="auto" width="400px" />
	    </div>
		<div class="row">
			@if(!empty($records))
			<div id="resp-table">
				<div id="resp-table-header">
					<div class="table-header-cell">
						NO
					</div>
					<div class="table-header-cell">
						BEADS
					</div>
					<div class="table-header-cell">
						SIZE
					</div>
					<div class="table-header-cell">
						APPROX. WT
					</div>

					<div class="table-header-cell">
						NO
					</div>
					<div class="table-header-cell">
						BEADS
					</div>
					<div class="table-header-cell">
						SIZE
					</div>
					<div class="table-header-cell">
						APPROX. WT
					</div>
				</div>

				<div id="resp-table-body">
					@for ($i = 0; $i < count($records); $i++) 
					@if(($i % 2) == 0)
					<div class="resp-table-row">
						<div class="table-body-cell">
							{{$records[$i]['code']}}
						</div>
						<div class="table-body-cell">
							<img src="{{$records[$i]['thumb_image']}}" alt="logo" style="width:40px;height:auto;" />
						</div>
						<div class="table-body-cell">
							{{$records[$i]['size_name']}}
						</div>
						<div class="table-body-cell">
							{{$records[$i]['weight']}}
						</div>

					@else
						<div class="table-body-cell">
							{{$records[$i]['code']}}
						</div>
						<div class="table-body-cell">
							<img src="{{$records[$i]['thumb_image']}}" alt="logo" style="width:40px;height:auto;" />
						</div>
						<div class="table-body-cell">
							{{$records[$i]['size_name']}}
						</div>
						<div class="table-body-cell">
							{{$records[$i]['weight']}}
						</div>
					</div>
					@endif
					@endfor
				</div>
			</div>
			@endif
		</div>
	</body>
</html>