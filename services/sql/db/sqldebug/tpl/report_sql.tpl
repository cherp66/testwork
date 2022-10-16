<div class="dedugger">
    <div class="level" >
    {$message}
    </div>
    <div class="mess">
         <strong>
         {$pref} Query in: </strong>{$file} 
         <strong> on line: </strong>{$line}
         <br />
         {$error}
    </div>
        <div class="listing">
            <div class="num">
               <code>
            <br />
               {$num}
            <br /> 
            <br /> 
               </code>
            </div>
            <div class="code">
                <code>
                    <pre style="color:#990099">
                    
                        {$sql}
                    </pre>
                </code>
            </div>                             
            <div class="clear"></div>
        </div>
        {$explain}
        <div class="mess">
    PHP:
        </div> 
        {$php}
        <div style="border:#8C999D 1px solid;">
        &nbsp;
        </div>   
</div>