<?php
if (!defined("IN_MOD"))
{
	die("Nah, I won't serve that file to you.");
}
reqPermission(1);
		if ((!empty($_GET['ip'])) && (filter_var($_GET['ip'], FILTER_VALIDATE_IP)))
		{
			?>
			<div class="box-outer top-box">
			<div class="box-inner">
			<div class="boxbar"><h2><?php printf($lang['mod/showing_posts'], $_GET['ip']); ?></h2></div>
			<div class="boxcontent">
			<a href="?/delete_posts&ip=<?php echo $_GET['ip']; ?>"><?php echo $lang['mod/delete_ip']; ?></a>
			<table>
			<thead>
			<tr>
			<td><?php echo $lang['mod/name']; ?></td>
			<td><?php echo $lang['mod/e_mail']; ?></td>
			<td><?php echo $lang['mod/date']; ?></td>
			<td><?php echo $lang['mod/comment']; ?></td>
			<td><?php echo $lang['mod/subject']; ?></td>
			<td><?php echo $lang['mod/file']; ?></td>
			<td><?php echo $lang['mod/delete']; ?></td>
			</tr>
			</thead>
			<tbody>
			<?php
			require_once( "./jbbcode/Parser.php" );
			$parser = new JBBCode\Parser();
			$bbcode = $conn->query("SELECT * FROM bbcodes;");
			
			while ($row = $bbcode->fetch_assoc())
			{
				$parser->addBBCode($row['name'], $row['code']);
			}
			$boards = $conn->query("SELECT * FROM boards ORDER BY short ASC");
			while ($board = $boards->fetch_assoc())
			{
				$posts = $conn->query("SELECT * FROM posts WHERE ip='".$_GET['ip']."' AND board='".$board['short']."'");
				while ($row = $posts->fetch_assoc())
				{
					echo "<tr><td>";
					
					$trip = "";
					if (!empty($row['trip']))
					{
						$trip = "<span class='postertrip'>!".$row['trip']."</span>";
					}
					if ($row['capcode'] == 1)
					{
						echo '<span class="nameBlock"><span class="name"><span style="color:#800080">'.$row['name'].'</span></span>'.$trip.' <span class="commentpostername"><span style="color:#800080">## Mod</span></span></span>';
					} elseif ($row['capcode'] == 2)
					{
						echo '<span class="nameBlock"><span class="name"><span style="color:#FF0000">'.$row['name'].'</span></span>'.$trip.' <span class="commentpostername"><span style="color:#FF0000">## Admin</span></span></span>';
					} elseif ($row['capcode'] == 3)
					{
						echo '<span class="nameBlock"><span class="name"><span style="color:#FF00FF">'.$row['name'].'</span></span>'.$trip.' <span class="commentpostername"><span style="color:#FF00FF">## Faggot</span></span></span>';
					} else {
						echo '<span class="nameBlock"><span class="name">'.$row['name'].'</span>'.$trip.'</span>';
					}
				
					echo "</td>";
					echo "<td>".$row['email']."</td>";
					echo "<td>".date("d/m/Y @ H:i", $row['date'])."</td>";
					if ($row['raw'] != 1)
					{
						if ($row['raw'] == 2)
						{
							$comment = processComment($board['short'], $conn, $row['comment'], $parser, 2, 0);
						} else {
							$comment = processComment($board['short'], $conn, $row['comment'], $parser, 2);
						}
					} else {
						$comment = $row['comment'];
					}
					echo "<td>".$comment."</td>";
					echo "<td>".$row['subject']."</td>";
					if (!empty($row['filename']))
					{
						
						if ($row['filename'] == "deleted")
						{
							echo "<td><img src='./img/deleted.gif' /></td>";
						} elseif (substr($row['filename'], 0, 8) == "spoiler:") {
							echo "<td><a href='./".$board['short']."/src/".substr($row['filename'], 8)."' target='_blank'><img src='./".$board['short']."/src/thumb/".substr($row['filename'], 8)."' /></a><br /><b>Spoiler image</b></td>";
						} elseif (substr($row['filename'], 0, 6) == "embed:") {
							echo "<td><a href='".substr($row['filename'], 6)."'>Embed</a></td>";
						} else {
							echo "<td><a href='./".$board['short']."/src/".$row['filename']."' target='_blank'><img src='./".$board['short']."/src/thumb/".$row['filename']."' /></a></td>";
						}
					} else {
						echo "<td></td>";
					}
					echo '<td>[<a href="?/delete_post&b='.$board['short'].'&p='.$row['id'].'">D</a>] [<a href="?/delete_post&b='.$board['short'].'&p='.$row['id'].'&f=1">F</a>] [<a href="?/bans/add&b='.$board['short'].'&p='.$row['id'].'">B</a>]</td>';
				}
			}
			?>
			</tbody>
			</table>
			</div>
			</div></div>
			<?php
		}
?>