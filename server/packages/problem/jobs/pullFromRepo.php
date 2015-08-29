<?php
class PullFromRepoJob implements IJob {
    private function run($script) {
        echo "<span>$</span> $script\n";
        echo htmlentities(shell_exec($script)) . "\n";
    }

    public function startexecute($args) {
        $this->run('echo $PWD');

        echo "<span>$</span> cd ..\n\n";
        chdir('..');

        $this->run("rm -rf *");
        $this->run("git clone -b Development https://github.com/The-Problem/The-Problem.git");
        $this->run("mv The-Problem/* .");
        $this->run("rm -rf The-Problem");
        $this->run("cp server/profile-production.php server/profile.php");
        $this->run("cp ../database.php server/database.php");

        echo "<span>$</span> php server/core/job.php importDB database.sql\n";
        Library::get('jobs');
        Jobs::execute("importDB", array("database.sql"));
    }

    public function endexecute() {

    }
}