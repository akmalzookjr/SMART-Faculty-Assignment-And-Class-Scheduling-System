<?php
include 'connect.php';

$query = "SELECT 
    Student.Stud_ID, 
    Student.Stud_Name, 
    Section.Section_Number, 
    Semester.Sem_Number, 
    Level.Level_Name, 
    COUNT(Course_Student.Course_ID) AS Total_Repeated_Courses
FROM student 
INNER JOIN section ON Student.Section_ID = Section.Section_ID
INNER JOIN semester ON Section.Sem_ID = Semester.Sem_ID
INNER JOIN level ON Semester.Level_ID = Level.Level_ID
LEFT JOIN course_student ON course_student.Stud_ID = student.Stud_ID
GROUP BY Student.Stud_ID, Student.Stud_Name, Section.Section_Number, Semester.Sem_Number, Level.Level_Name
ORDER BY Section.Section_Number, Student.Stud_Name";

$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Stud_Name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Section_Number']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Sem_Number']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Level_Name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Total_Repeated_Courses']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>No students found.</td></tr>";
}
mysqli_close($conn);
?>
