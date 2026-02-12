$s=file_get_contents(__DIR__.'/../app/Providers/RouteServiceProvider.php');
$pairs=['('=>')','['=>']','{'=>'}'];
$stack=[];
for($i=0;$i<strlen($s);$i++){
    $c=$s[$i];
    if(isset($pairs[$c])){
        $stack[]=[$c,$i];
    } elseif(in_array($c,$pairs)){
        $last=array_pop($stack);
        if(!$last || $pairs[$last[0]]!=$c){
            $line = substr_count(substr($s,0,$i), "\n") + 1;
            echo "Mismatch at line $line pos $i char '$c' expected closing for ".($last? $last[0]:'none')."\n";
            exit(0);
        }
    }
}
if(count($stack)){
    $last=end($stack);
    $line = substr_count(substr($s,0,$last[1]), "\n") + 1;
    echo "Unclosed '".$last[0]."' at line ".$line." pos ".$last[1]."\n";
} else {
    echo "All balanced\n";
}
