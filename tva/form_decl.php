<style type="text/css">
   h1 {
 color : blue;
 font-size:120%;
 }
   table tr {
     font-size:12px;
   }
</style>
<h1>Cadre II : Opérations à la sortie </h1>
<table>
<tr>
<td>
A. Opérations soumises à un régime particulier [00]
</td>
<td>
<?=$str_00;?>
</td>
</tr><tr>
<td>
 B. Opérations pour lesquelles la T.V.A. est due par le déclarant :</td></td>
 </tr><tr>
 <td>&nbsp;&nbsp;&nbsp;&nbsp;- au taux de 6% [01]</td>
<td>
<?=$str_01;?>
</td>
 </tr><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;- au taux de 12% [02]</td>
<td>
<?=$str_02;?>
</td>
 </tr><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;- au taux de 21% [03] </td>
<td>
<?=$str_03;?>
</td>
</tr><tr>
<td>
C. Services pour lesquels la T.V.A. étrangère est due par le cocontractant [44]
</td>
<td>
<?=$str_44;?>
</td> 
</tr><tr>
<td>
D. Opérations pour lesquelles la T.V.A. est due par le cocontractant [45]
</td>
<td>
<?=$str_45;?>
</td> 
</tr><tr>
<td>
E. Livraisons intracommunautaires exemptées effectuées en Belgique et ventes ABC [46]
</td>
<td>
<?=$str_46;?>
</td> 
</tr><tr>
<td>
 F. Autres opérations exemptées et autres opérations effectuées à l'étranger [47]
</td>
<td>
<?=$str_47;?>
</td> 
</tr><tr>
<td>
G. Montant des notes de crédit délivrées et des corrections négatives : </td>
 </tr><tr>
 <td>&nbsp;&nbsp;&nbsp;&nbsp;- relatif aux opérations inscrites en grilles 44 et 46 [48] </td>
<td>
<?=$str_48;?>
</td> 

 </tr><tr>
 <td>&nbsp;&nbsp;&nbsp;&nbsp;- relatif aux autres opérations du cadre II [49]</td>
<td>
<?=$str_49;?>
</td> 

 </tr><tr>
 </table>

<h1>Cadre III : Opérations à l'entrée</h1>
<table>
<tr>
<td>A. Montant des opérations à l'entrée compte tenu des notes de crédit reçues et autres corrections : </td>
</tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;&nbsp;- marchandises, matières premières et matières auxiliaires [81] </td>
<td>
<?=$str_81;?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;&nbsp;- services et biens divers [82]  </td>
<td>
<?=$str_82;?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;&nbsp;- biens d'investissement [83]  </td>
<td>
<?=$str_83;?>
</td>
</tr>
<tr>
<td>B. Montant des notes de crédit reçues et des corrections négatives : </td>
</tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;&nbsp;- relatif aux opérations inscrites en grilles 86 et 88 [84]  </td>
<td>
<?=$str_84;?>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;&nbsp;- relatif aux autres opérations du cadre III [85]  </td>
<td>
<?=$str_85;?>
</td>
</tr>
<tr>
<td>C. Acquisitions intracommunautaires effectuées en Belgique et ventes ABC [86] </td>
<td>
<?=$str_86;?>
</td>
</tr>
<tr>
<td>
D. Autres opérations à l'entrée pour lesquelles la T.V.A. est due par le déclarant [87] </td>
<td><?=$str_87?></td>
</tr><tr>
<td> E. Services intracommunautaires avec report de perception [88]</td>
<td>
<?=$str_88?>
</td>
</table>

<h1>Cadre IV : </h1>
<table>
<tr><td>Taxes dues A. T.V.A. relative aux opérations déclarées en : </td>
</tr>
<tr>
<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;-grilles 01, 02 et 03 [54]</td>
<td>
<?=$str_54;?>
</td>
</tr>
<td>&nbsp;&nbsp;&nbsp;&nbsp;- grilles 86 et 88 [55] </td>
<td>
<?=$str_55;?>
</td>
</tr>
<tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;&nbsp;- grille 87, à l'exception des importations avec report de perception [56] </td>
<td><?=$str_56;?></td>
</tr>
<tr>
<td>
B. T.V.A. relative aux importations avec report de perception [57] </td>
<td><?=$str_57;?></td>
</tr>
<tr>
<td>

C. Diverses régularisations T.V.A. en faveur de l'Etat [61]</td> 
<td></td>
</tr>
<tr>
<td>
D. T.V.A. à reverser mentionnée sur les notes de crédit reçues [63]
</td>
<td><?=$str_63;?></td>
</tr>
<tr>
<td>
Total des grilles 54, 55, 56, 57, 61 et 63 [XX]
</td>
<td>
<?=$str_xx;?>
</td>
</tr>
</table>
<h1>Cadre V : Taxes déductibles, solde et acompte</h1>
<table>
<tr>
<td>
A.T.V.A. déductible  [59]
</td>
<td>
<?=$str_59;?>
</td>
</tr>
<tr>
<td>B.Diverses régularisations T.V.A. en faveur du déclarant  [62]
</td>
<td>
<?=$str_62;?>
</td>
</tr>
<tr>
<td>C.T.V.A. à récupérer mentionnée sur les notes de crédit délivrées [64]
</td>
<td>
<?=$str_64;?>
</td>
</tr>
<tr>
<td>
Total des grilles 59, 62 et 64[yy]
</td>
<td><?=$str_yy;?></td>
</tr>
</table>

<h1>Cadre VI : Solde*</h1>
<table>
<tr>
<td>Taxe due à l'Etat : grille xx - grille yy [71]</td>
<td><?=$str_71;?></td>
</tr>
<tr>
<td>Sommes dues par l'Etat : grille yy - grille xx [72]</td>
<td><?=$str_82;?></td>
</tr>
</table>
<span class="notice">*Une seule des grilles 71 ou 72 peut être remplie </span>

<h1>Cadre VII : Acompte*</h1>
<table>
<tr>
<td>
T.V.A. réellement due pour la période du 1er au 20 décembre[91]
</td>
<td><?=$str_91;?></td>
</tr>
</table>
<span class="notice">*Concerne uniquement la déclaration mensuelle de décembre</span>

