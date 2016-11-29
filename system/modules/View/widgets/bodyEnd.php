<?php
/**
 * Body end
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
?>
<div id="loading-indicator">
    <div class ='loading-circle animated fadeInLeft'></div>
    <div class ='loading-circle animated fadeInLeft2'></div>
    <div class ='loading-circle animated fadeInLeft3'></div>
</div>
<style>
    .loading-circle {
      background: #000;
      border-radius: 50%;
      height:10px;
      width:10px;
      position:absolute;
    }
    #loading-indicator{
      position: fixed;
      bottom:15px;
      left:0%;
      text-align: center;
      color:#red;
      font-size: 20px;
      width:100%;
    }
    .animated { 
      -webkit-animation-duration: 5s; 
      animation-duration: 5s; 
      animation-iteration-count:infinite; 
      -webkit-animation-iteration-count:infinite; 
    } 

    @-webkit-keyframes fadeInLeft { 
      0% { 
        opacity: 0; 
        left:0;
      } 
      50% { 
        opacity: 1; 
        left:50%;
      } 
      100% { 
        opacity: 0; 
        left:100%;
      }  
    } 
    @keyframes fadeInLeft { 
      0% { 
        opacity: 0; 
        left:0;
      } 
      50% { 
        opacity: 1; 
        left:50%;
      } 
      100% { 
        opacity: 0; 
        left:100%;
      } 
    } 
    .fadeInLeft { 
      -webkit-animation-name: fadeInLeft; 
      animation-name: fadeInLeft; 
    }
    @-webkit-keyframes fadeInLeft2 { 
      0% { 
        opacity: 0; 
        left:0;
      } 
      40% { 
        opacity: 1; 
        left:50%;
      } 
      100% { 
        opacity: 0; 
        left:100%;
      }  
    } 
    @keyframes fadeInLeft2 { 
      0% { 
        opacity: 0; 
        left:0;
      } 
      40% { 
        opacity: 1; 
        left:50%;
      } 
      100% { 
        opacity: 0; 
        left:100%;
      } 
    } 
    .fadeInLeft2 { 
      -webkit-animation-name: fadeInLeft2; 
      animation-name: fadeInLeft2; 
    }
    @-webkit-keyframes fadeInLeft3 { 
      0% { 
        opacity: 0; 
        left:0;
      } 
      60% { 
        opacity: 1; 
        left:50%;
      } 
      100% { 
        opacity: 0; 
        left:100%;
      }  
    } 
    @keyframes fadeInLeft3 { 
      0% { 
        opacity: 0; 
        left:0;
      } 
      60% { 
        opacity: 1; 
        left:50%;
      } 
      100% { 
        opacity: 0; 
        left:100%;
      } 
    } 
    .fadeInLeft3 { 
      -webkit-animation-name: fadeInLeft3; 
      animation-name: fadeInLeft3; 
    }
</style>
<script>
    inji.start(<?= json_encode($options); ?>);
</script>