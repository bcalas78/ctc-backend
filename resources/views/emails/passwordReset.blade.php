<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta http-equiv="X-UA-Compatible" content="ie=edge" />
		<title>Réinitialisation du mot de passe</title>

		<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

		<style>
			.password-body {
				display: block;
				padding: 0px;
				margin: 0px;
			}

			.password-wrapper {
				width: 100%;
				display: block;
				overflow: hidden;
				box-sizing: border-box;
				color: #222;
				background: #f2f2fd;
				font-size: 18px;
				font-weight: normal;
				font-family: 'Baloo 2', 'Open Sans', 'Roboto', 'Segoe UI', 'Helvetica Neue', Helvetica, Tahoma, Arial, monospace, sans-serif;
			}

			.password-table {
				border-collapse: collapse;
				border-spacing: 0;
				border: 0px;
				width: 640px;
				max-width: 90%;
				margin: 100px auto;
				box-shadow: 0px 20px 48px rgba(0, 0, 0, 0.2);
				border-radius: 10px;
				overflow: hidden;
			}

			.password-table tr {
				background: #ffffff;
			}

			.password-table td,
			.password-table th {
				border: 0px;
				border-spacing: 0;
				border-collapse: collapse;
			}

			.password-table tr td {
				padding: 0px 40px;
				box-sizing: border-box;
			}

			.password-margin {
				float: left;
				width: 100%;
				overflow: hidden;
				height: 40px;
				padding-bottom: 0px;
				box-sizing: border-box;
			}

			.password-div {
				float: left;
				width: 100%;
				overflow: hidden;
				box-sizing: border-box;
			}

			.password-table h1,
			.password-table h2,
			.password-table h3,
			.password-table h4 {
				float: left;
				width: 100%;
				margin: 0px 0px 20px 0px !important;
				padding: 0px;
			}

			.password-table h1 {
				font-size: 33px;
			}

			.password-table h2 {
				font-size: 26px;
			}

			.password-table h3 {
				font-size: 23px;
			}

			.password-table p {
				float: left;
				width: 100%;
				font-size: 18px;
				margin: 0px 0px 20px 0px !important;
			}

			.password-table h4 {
				font-size: 20px;
			}

			.password-table a {
				color: #6d49fc;
				font-weight: bold;
			}

			.password-table a:hover {
				color: #55cc55;
			}

			.password-table a:active {
				color: #ff6600;
			}

			.password-table a:visited {
				color: #ff00ff;
			}

			.password-table a.password-link {
				display: inline-block;
				width: auto !important;
				outline: none !important;
				text-decoration: none !important;
			}

			.password-table img,
			.password-table a img {
				display: block;
				max-width: 100%;
				margin-bottom: 20px;
				border: 0px;
				border-radius: 10px;
				overflow: hidden;
			}

			.password-table a.password-button {
				display: inline-block;
				font-weight: bold;
				font-size: 17px;
				padding: 15px 40px;
				margin: 20px 0px;
				color: #ffffff !important;
				background: #6d49fc !important;
				border-radius: 10px;
				text-decoration: none;
				outline: none;
			}

			.password-table a.password-button:hover {
				color: #ffffff !important;
				background: #55cc55 !important;
			}

		</style>
	</head>

	<body class="password-body">
		<div class="password-wrapper">
			<table class="password-table">
				<tbody>
					<tr class="password-tr">
						<td class="password-td" colspan="10" style="">
							<div class="password-margin"></div>
							<center>
								<h1>Bienvenue sur le site CTC !</h1>
								<img src="https://images.unsplash.com/photo-1520399636535-24741e71b160?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Logo CTC" height="165" />
							</center>
							<h2>Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe:</h2>
						</td>
					</tr>

					<tr class="password-tr">
						<td class="password-td" colspan="10" style="">
							<center>
                                <a href="http://localhost:5173/reset-password/{{$token}}" class="password-button" target="_blank">Réinitialiser mon mot de passe</a>
							</center>
						</td>
					</tr>

					<tr class="password-tr">
						<td class="password-td" colspan="10" style="">
							<h3>Cordialement,</h3>
							<p>L'équipe de CTC</p>
						</td>
					</tr>

				</tbody>
			</table>
		</div>
	</body>
</html>