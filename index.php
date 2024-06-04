<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Member Popup</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">


</head>
<body>

<?php
class Member {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function fetchMembers() {
        $stmt = $this->pdo->query("SELECT * FROM Members");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buildTree($members, $parentId = 0) {
        $branch = array();
        foreach ($members as $member) {
            if ($member['ParentId'] == $parentId) {
                $children = $this->buildTree($members, $member['Id']);
                if ($children) {
                    $member['children'] = $children;
                }
                $branch[] = $member;
            }
        }
        return $branch;
    }

    public function displayTree($tree) {
        echo '<ul>';
        foreach ($tree as $node) {
            echo '<li data-id="' . $node['Id'] . '">' . $node['Name'];
            if (isset($node['children'])) {
                $this->displayTree($node['children']);
            }
            echo '</li>';
        }
        echo '</ul>';
    }
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=john_doe', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $member = new Member($pdo);
    $members = $member->fetchMembers();
    $tree = $member->buildTree($members);
    $member->displayTree($tree);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>

<!-- Add Member Button -->
<button id="addMemberBtn" class="btn btn-primary" data-toggle="modal" data-target="#addMemberModal">Add Member</button>

<!-- Popup Modal -->
<div id="addMemberModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Member</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addMemberForm">
                    <div class="form-group">
                        <label for="parent">Parent</label>
                        <select id="parent" name="parent" class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="script.js"></script>
<script>
    $(document).ready(function() {
    $('#addMemberBtn').click(function() {
        // Fetch members for the dropdown
        $.ajax({
            url: 'get_members.php',
            method: 'GET',
            success: function(data) {
                $('#parent').html(data);
            }
        });
    });

    $('#addMemberForm').submit(function(e) {
        e.preventDefault();
        var name = $('#name').val();
        var parentId = $('#parent').val();
        if (name === '') {
            alert('Name cannot be empty');
            return false;
        }
        $.ajax({
            url: 'add_member.php',
            method: 'POST',
            data: {name: name, parent: parentId},
            success: function(data) {
                var newMember = JSON.parse(data);
                var newLi = $('<li data-id="' + newMember.Id + '">').text(newMember.Name);
                if (parentId == 0) {
                    $('ul').append(newLi);
                } else {
                    $('li[data-id="' + parentId + '"]').append('<ul>').append(newLi);
                }
                $('#addMemberModal').modal('hide'); // Hide the modal after adding the member
            }
        });
    });
});

</script>
</body>
</html>
