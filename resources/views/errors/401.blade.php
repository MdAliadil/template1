<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Oops Something went wrong</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet">
    <style type="text/css">
        * {
          -webkit-box-sizing: border-box;
                  box-sizing: border-box;
        }

        body {
          padding: 0;
          margin: 0;
        }

        #notfound {
          position: relative;
          height: 100vh;
          background: #f6f6f6;
        }

        #notfound .notfound {
          position: absolute;
          left: 50%;
          top: 50%;
          -webkit-transform: translate(-50%, -50%);
              -ms-transform: translate(-50%, -50%);
                  transform: translate(-50%, -50%);
        }

        .notfound {
          max-width: 767px;
          width: 100%;
          line-height: 1.4;
          padding: 110px 40px;
          text-align: center;
          background: #fff;
          -webkit-box-shadow: 0 15px 15px -10px rgba(0, 0, 0, 0.1);
                  box-shadow: 0 15px 15px -10px rgba(0, 0, 0, 0.1);
        }

        .notfound .notfound-404 {
          position: relative;
          height: 180px;
        }

        .notfound .notfound-404 h1 {
          font-family: 'Roboto', sans-serif;
          position: absolute;
          left: 50%;
          top: 50%;
          -webkit-transform: translate(-50%, -50%);
              -ms-transform: translate(-50%, -50%);
                  transform: translate(-50%, -50%);
          font-size: 165px;
          font-weight: 700;
          margin: 0px;
          color: #262626;
          text-transform: uppercase;
        }

        .notfound .notfound-404 h1>span {
          color: #00b7ff;
        }

        .notfound h2 {
          font-family: 'Roboto', sans-serif;
          font-size: 22px;
          font-weight: 400;
          text-transform: uppercase;
          color: #151515;
          margin-top: 0px;
          margin-bottom: 25px;
        }

        .notfound .notfound-search {
          position: relative;
          max-width: 320px;
          width: 100%;
          margin: auto;
        }

        .notfound .notfound-search>input {
          font-family: 'Roboto', sans-serif;
          width: 100%;
          height: 50px;
          padding: 3px 65px 3px 30px;
          color: #151515;
          font-size: 16px;
          background: transparent;
          border: 2px solid #c5c5c5;
          border-radius: 40px;
          -webkit-transition: 0.2s all;
          transition: 0.2s all;
        }

        .notfound .notfound-search>input:focus {
          border-color: #00b7ff;
        }

        .notfound .notfound-search>button {
          position: absolute;
          right: 15px;
          top: 5px;
          width: 40px;
          height: 40px;
          text-align: center;
          border: none;
          background: transparent;
          padding: 0;
          cursor: pointer;
        }

        .notfound .notfound-search>button>span {
          width: 15px;
          height: 15px;
          position: absolute;
          left: 50%;
          top: 50%;
          -webkit-transform: translate(-50%, -50%) rotate(-45deg);
              -ms-transform: translate(-50%, -50%) rotate(-45deg);
                  transform: translate(-50%, -50%) rotate(-45deg);
          margin-left: -3px;
        }

        .notfound .notfound-search>button>span:after {
          position: absolute;
          content: '';
          width: 10px;
          height: 10px;
          left: 0px;
          top: 0px;
          border-radius: 50%;
          border: 4px solid #c5c5c5;
          -webkit-transition: 0.2s all;
          transition: 0.2s all;
        }

        .notfound-search>button>span:before {
          position: absolute;
          content: '';
          width: 4px;
          height: 10px;
          left: 7px;
          top: 17px;
          border-radius: 2px;
          background: #c5c5c5;
          -webkit-transition: 0.2s all;
          transition: 0.2s all;
        }

        .notfound .notfound-search>button:hover>span:after {
          border-color: #00b7ff;
        }

        .notfound .notfound-search>button:hover>span:before {
          background-color: #00b7ff;
        }

        @media only screen and (max-width: 767px) {
          .notfound h2 {
            font-size: 18px;
          }
        }

        @media only screen and (max-width: 480px) {
          .notfound .notfound-404 h1 {
            font-size: 141px;
          }
        }

    </style>
</head>

<body>
    <div id="notfound">
        <div class="notfound">
            <div class="notfound-404">
                <h1>4<span>0</span>1</h1>
            </div>
            <h2>Unauthorize Access, Please contact administration</h2>
            <form class="notfound-search">
                <a href="{{ route('home') }}"><button type="button" style="padding: 11px;font-size: 20px;background: #46b7f8;color: white;"><span>Go To Dashboard</span></button></a>
            </form>
        </div>
    </div>
</body>
</html>
