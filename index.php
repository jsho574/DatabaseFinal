<?php
	require('web_utils.php');
	
	session_start();
	
	$message = '';
	
	$target = $_GET['target'];
	$action = $_POST['action'];
    //$table = $_POST['tableOne'];
	$data = null;
	
	switch($action) {
		case 'delete':
			$message = deleteTask();
			break;
		case 'add':
			list($target, $message, $data) = addTask();
			break;
//		case 'set_completed':
//			$message = setCompletionStatus('completed');
//			break;
//		case 'set_not_completed':
//			$message = setCompletionStatus('not completed');
//			break;
		case 'edit':
			list($target, $message, $data) = editTask();
			break;
		case 'update':
			list($target, $message, $data) = updateTask();
	}
	
	switch($target) {
		case 'taskform':
			presentTaskForm($message, $data);
			break;
		default:
			presentTaskList($message);
	}
	
	

	// functions are defined below
	// eventually these will be moved to individual php files
	
	function presentTaskList($message = "") {
        $title = "Players";
		$stylesheet = 'taskmanager.css';
		
//		//$orderBy = $_SESSION['orderby'] ? $_SESSION['orderby'] : 'player_name';
//        $orderBy = 'player_name';
//		$orderDirection = $_SESSION['orderdirection'] ? $_SESSION['orderdirection'] : 'asc';
//		
//		if ($_GET['orderby']) {
//			if ($orderBy == $_GET['orderby']) {
//				if ($orderDirection == 'asc') {
//					$orderDirection = 'desc';
//				} else {
//					$orderDirection = 'asc';
//				}
//			} else {
//				$orderDirection = 'asc';
//			}
//			$orderBy = $_GET['orderby'];
//		}
//		
//		$_SESSION['orderby'] = $orderBy;
//		$_SESSION['orderdirection'] = $orderDirection;
	
		$tasks = array();

		// Create connection
		require('db_credentials.php');
		$mysqli = new mysqli($servername, $username, $password, $dbname);
	
		if ($mysqli->connect_error) {
			$message = $mysqli->connect_error;
            $title = "here1";
		} else {
//			$orderBy = $mysqli->real_escape_string($orderBy);
//			$orderDirection = $mysqli->real_escape_string($orderDirection);
			//$sql = "SELECT * FROM Player_Stats ORDER BY $orderBy $orderDirection";
            $sql = "SELECT * FROM Player_Stats";
			if ($result = $mysqli->query($sql)) {
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						array_push($tasks, $row);
					}
				}
				$result->close();
			} else {
				$message = $mysqli->error;
                $title = "here2";
			}
			$mysqli->close();
		}

//		print generatePageHTML("Players", generateTaskTableHTML($tasks, $message, $orderBy, $orderDirection, $title), $stylesheet);
        print generatePageHTML("Players", generateTaskTableHTML($tasks, $essage, $title), $stylesheet);
	}
	
//	function generateTaskTableHTML($tasks, $message, $orderBy, $orderDirection, $title) {
function generateTaskTableHTML($tasks, $message, $title) {
		$html = "<h1>$title</h1>\n";
		
		if ($message) {
			$html .= "<p class='message'>$message</p>\n";
		}
		
		$html .= "<p><a class='taskButton' href='index.php?target=taskform'>+ Add Player</a></p>";
//        if($table == 1){
//        $html .= "<p><a class='taskButton' href='index.php?target=table2'>+ Go to Player measurements table</a></p>"
//        }
		if (count($tasks) < 1) {
			$html .= "<p>No tasks to display!</p>\n";
			return $html;
		}
	   
		$html .= "<table>\n";
        $html .= "<tr><th>delete</th><th>edit</th>";
//        $html .= "<tr><th>delete</th><th>edit</th><th>player name</th><th>position</th><th>points</th><th>assists</th><th>steals</th>";
		
		$columns = array(array('name' => 'player_Name', 'label' => 'player_Name'),
                         array('name' => 'position', 'label' => 'position'),
                         array('name' => 'points', 'label' => 'points'), 
						 array('name' => 'assists', 'label' => 'assists'), 
						 array('name' => 'steals', 'label' => 'steals'));
		
		// geometric shapes in unicode
		// http://jrgraphix.net/r/Unicode/25A0-25FF
		foreach ($columns as $column) {
			//$name = $column['name'];
			$label = $column['label'];
//			if ($name == $orderBy) {
//				if ($orderDirection == 'asc') {
//					$label .= " &#x25BC;";  // ▼
//				} else {
//					$label .= " &#x25B2;";  // ▲
//				}
//			}
//			$html .= "<th><a class='order' href='index.php?orderby=$name'>$label</a></th>";
            $html .= "<th><a class='order' >$label</a></th>";
		}
	
		foreach ($tasks as $task) {
			$id = $task['player_Num'];
			$Name = $task['player_name'];
			$Pos = $task['position'];
			$points = $task['points'];
			$assists = $task['assists'];
			$steals = $task['steals'];
			
//			$completedAction = 'set_completed';
//			$completedLabel = 'not completed';
//			if ($completedDate) {
//				$completedAction = 'set_not_completed';
//				$completedLabel = 'completed';
//			}
			
			$html .= "<tr>";
			$html .= "<td><form action='index.php' method='post'><input type='hidden' name='action' value='delete' /><input type='hidden' name='id' value='$id' /><input type='submit' value='Delete'></form></td>";
			$html .= "<td><form action='index.php' method='post'><input type='hidden' name='action' value='edit' /><input type='hidden' name='id' value='$id' /><input type='submit' value='Edit'></form></td>";
//			$html .= "<td><form action='index.php' method='post'><input type='hidden' name='action' value='$completedAction' /><input type='hidden' name='id' value='$id' /><input type='submit' value='$completedLabel'></form></td>";
			$html .= "<td>$Name</td><td>$Pos</td><td>$points</td><td>$assists</td><td>$steals</td>";
			$html .= "</tr>\n";
		}
		$html .= "</table>\n";
	
		return $html;
	}
	
	function deleteTask() {
		$id = $_POST['id'];
	
		$message = "";
	
		if (!$id) {
			$message = "No Player was specified to delete.";
		} else {
			// Create connection
			require('db_credentials.php');
			$mysqli = new mysqli($servername, $username, $password, $dbname);
			// Check connection
			if ($mysqli->connect_error) {
				$message = $mysqli->connect_error;
			} else {
				$id = $mysqli->real_escape_string($id);
				$sql = "DELETE FROM Player_Stats WHERE player_Num = $id";
				if ( $result = $mysqli->query($sql) ) {
					$message = "Player was deleted.";
				} else {
					$message = $mysqli->error;
				}
                $sql = "DELETE FROM Players WHERE player_Num = $id";
				if ( $result = $mysqli->query($sql) ) {
					$message = "Player was deleted.";
				} else {
					$message = $mysqli->error;
				}
				$mysqli->close();
			}
		}
	
		return $message;
	}
	
//	function setCompletionStatus($status) {
//		$id = $_POST['id'];
//	
//		$message = "";
//		
//		$completedDate = 'null';
//		if ($status == 'completed') {
//			$completedDate = 'NOW()';
//		}
//	
//		if (!$id) {
//			$message = "No task was specified to change completion status.";
//		} else {
//			// Create connection
//			require('db_credentials.php');
//			$mysqli = new mysqli($servername, $username, $password, $dbname);
//			// Check connection
//			if ($mysqli->connect_error) {
//				$message = $mysqli->connect_error;
//			} else {
//				$id = $mysqli->real_escape_string($id);
//				$sql = "UPDATE tasks SET completedDate = $completedDate WHERE id = '$id'";
//				if ( $result = $mysqli->query($sql) ) {
//					$message = "Task was updated to $status.";
//				} else {
//					$message = $mysqli->error;
//				}
//				$mysqli->close();
//			}
//		}
//	
//		return $message;
//	}
	
	function presentTaskForm($message = "", $data = null) {
        $Name = '';
		$Position = '';
        $height_feet = '';
        $height_inches = '';
        $weight = '';
	
		if ($data) {
			$Name = $data['Name'];
			$Position = $data['position'];
            $height_feet = $data['height_feet'];
            $height_inches = $data['height_inches'];
            $weight = $data['weight'];
			
		}
//		$category = '';
//		$title = '';
//		$description = '';
//		$selected = array('personal' => '', 'school' => '', 'work' => '', 'uncategorized' => '');
//		if ($data) {
//			$category = $data['category'] ? $data['category'] : 'uncategorized';
//			$title = $data['title'];
//			$description = $data['description'];
//			$selected[$category] = 'selected';
//		} else {
//			$selected['uncategorized'] = 'selected';
//		}
	
		$html = <<<EOT1
<!DOCTYPE html>
<html>
<head>
<title>Player Manager</title>
<link rel="stylesheet" type="text/css" href="taskmanager.css">
</head>
<body>
<h1>Tasks</h1>
EOT1;

		if ($message) {
			$html .= "<p class='message'>$message</p>\n";
		}
		
		$html .= "<form action='index.php' method='post'>";
		
		if ($data['id']) {
			$html .= "<input type='hidden' name='action' value='update' />";
			$html .= "<input type='hidden' name='id' value='{$data['id']}' />";
		} else {
			$html .= "<input type='hidden' name='action' value='add' />";
		}
		
		$html .= <<<EOT2
  
  <p>Name<br />
  <input type="text" name="Name" value="$Name" placeholder="Name" maxlength="255" size="80"></p>

  <p>Position<br />
  <textarea name="position" rows="2" cols="4" placeholder="">$Position</textarea></p>
  <p>Height in Feet<br />
  <input type="text" pattern="\d+" name="height_feet" value="$height_feet" maxlength="2" size="4"></p>
  <p>Height in inches<br />
  <input type="text" pattern="\d+" name="height_inch" value="$height_inch" maxlength="3" size="4"></p>
  <p>Weight<br />
  <input type="text" pattern="\d+" name="weight" value="$weight" maxlength="6" size="7"></p>
  <input type="submit" name='submit' value="Submit"> <input type="submit" name='cancel' value="Cancel">
</form>
</body>
</html>
EOT2;

		print $html;
	}
	
	function addTask() {
        $message = '';
		
		if ($_POST['cancel']) {
			$message = 'Adding new Player was cancelled.';
			return array('', $message);
		}
		
		if (! $_POST['Name']) {
			$message = 'A Name is required.';
			return array('playerform', $message, $_POST);
		}
        	if (! $_POST['position']) {
			$message = 'A Position is required.';
			return array('playerform', $message, $_POST);
		}
        	if (! $_POST['height_feet']) {
			$message = 'A height in feet is required.';
			return array('playerform', $message, $_POST);
		}
        	if (! $_POST['height_inch']) {
			$message = 'A height in inches is required.';
			return array('playerform', $message, $_POST);
		}
        	if (! $_POST['weight']) {
			$message = 'A weight is required.';
			return array('playerform', $message, $_POST);
		}
	
		$Name = $_POST['Name'];
        $Position = $_POST['position'];
        $height_feet = $_POST['height_feet'];
        $height_inch = $_POST['height_inch'];
        $weight = $_POST['weight'];

		// Create connection
		require('db_credentials.php');
		$mysqli = new mysqli($servername, $username, $password, $dbname);

		// Check connection
		if ($mysqli->connect_error) {
			$message = $mysqli->connect_error;
		} else {
			$category = $mysqli->real_escape_string($category);
			$title = $mysqli->real_escape_string($title);
			$description = $mysqli->real_escape_string($description);
	        $sql = "INSERT INTO Player_Stats (player_name, Position) VALUES ('$Name', '$Position')";
            $sql2 = "INSERT INTO Players (player_name, height_feet, height_inches, weight) VALUES('$Name','$height_feet','$height_inch','$weight')";
			
	
			if ($result = $mysqli->query($sql) && $result = $mysqli->query($sql2)) {
				$message = "Player was added";
			} else {
				$message = $mysqli->error;
			}
            
		}
		
		return array('', $message);
	}
//		$message = '';
//		
//		if ($_POST['cancel']) {
//			$message = 'Adding new task was cancelled.';
//			return array('', $message);
//		}
//		
//		if (! $_POST['title']) {
//			$message = 'A title is required.';
//			return array('taskform', $message, $_POST);
//		}
//	
//		$title = $_POST['title'];
//		$category = $_POST['category'] ? $_POST['category'] : 'uncategorized';
//		$description = $_POST['description'] ? $_POST['description'] : "";
//
//		// Create connection
//		require('db_credentials.php');
//		$mysqli = new mysqli($servername, $username, $password, $dbname);
//
//		// Check connection
//		if ($mysqli->connect_error) {
//			$message = $mysqli->connect_error;
//		} else {
//			$category = $mysqli->real_escape_string($category);
//			$title = $mysqli->real_escape_string($title);
//			$description = $mysqli->real_escape_string($description);
//	
//			$sql = "INSERT INTO tasks (title, description, category, addDate) VALUES ('$title', '$description', '$category', NOW())";
//	
//			if ($result = $mysqli->query($sql)) {
//				$message = "Task was added";
//			} else {
//				$message = $mysqli->error;
//			}
//
//		}
//		
//		return array('', $message);
//	}
//	
	function editTask() {
		$id = $_POST['id'];
	
		$message = "";
	
		if (!$id) {
			$message = "No player was specified to edit.";
			return array('', $message);
		} else {
			// Create connection
			require('db_credentials.php');
			$mysqli = new mysqli($servername, $username, $password, $dbname);
			// Check connection
			if ($mysqli->connect_error) {
				$message = $mysqli->connect_error;
				return array('', $message);
			} else {
				$id = $mysqli->real_escape_string($id);
				$sql = "SELECT * FROM tasks WHERE player_Num = $id";
				if ( $result = $mysqli->query($sql) ) {
					if ($result->num_rows > 0) {
						$data = $result->fetch_assoc();
						$result->close();
						$mysqli->close();
						return array('taskform', '', $data);
					} else {
						$message = "No task was found to edit.";
						$mysqli->close();
						return array('', $message);					
					}
				} else {
					$message = $mysqli->error;
					return array('', $message);
				}
				
			}
		}
	
	}
	
	function updateTask() {
		$message = "";
		
		if ($_POST['cancel']) {
			$message = 'Editing task was cancelled.';
			return array('', $message);
		}
		
		$id = $_POST['id'];

		if (!$id) {
			$message = "No task was specified to update.";
			return array('', $message);
		}		
		
		$title = $_POST['title'];
		$description = $_POST['description'];
		$category = $_POST['category'];
		
		if (!$title) {
			$message = 'A title is required.';
			return array('taskform', $message, $_POST);
		}
	
		// Create connection
		require('db_credentials.php');
		$mysqli = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($mysqli->connect_error) {
			$message = $mysqli->connect_error;
			return array('', $message);
		} else {
			$id = $mysqli->real_escape_string($id);
			$title = $mysqli->real_escape_string($title);
			$description = $mysqli->real_escape_string($description);
			$category = $mysqli->real_escape_string($category);
			$sql = "UPDATE tasks SET title='$title', description='$description', category='$category' WHERE id = $id";
			if ( $result = $mysqli->query($sql) ) {
				$message = 'Task was updated.';	
			} else {
				$message = $mysqli->error;
			}
			return array('', $message);
			$mysqli->close();
		}
	
	}
	
?>