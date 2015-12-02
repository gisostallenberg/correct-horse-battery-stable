<?php

namespace GisoStallenberg\CorrectHorseBatteryStaple;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class CorrectHorseBatteryStaple {
    /**
     * The messages and coresponding statusses
     *
     * @var array
     */
    private $messageStatus = [
        'error' => -1,
        'OK' => 0,
        'unknown' => 1,
        'it does not contain enough DIFFERENT characters' => 100,
        'it is all whitespace' => 101,
        'it is based on a (reversed) dictionary word' => 102,
        'it is based on a dictionary word' => 103,
        'it is based on your username' => 104,
        'it is based upon your password entry' => 105,
        'it is derivable from your password entry' => 106,
        'it is derived from your password entry' => 107,
        'it is too short' => 108,
        'it is too simplistic/systematic' => 109,
        'it is WAY too short' => 110,
        'you are not registered in the password file' => 111,
    ];

    /**
     * The command to use
     *
     * @var string
     */
    private $command;

    /*
     * Constructor
     */
    public function __construct() {
        $this->command = $this->verifyCommand();
    }

    /**
     * Verify the command to be available
     *
     * @return string
     * @throws RuntimeException
     */
    private function verifyCommand() {
        $process = new Process('which cracklib-check');
        $process->setTimeout(10);
        $process->run();

        if ($process->isSuccessful()) {
            return trim($process->getOutput() );
        }

        $process = new Process('whereis cracklib-check');
        $process->setTimeout(10);
        $process->run();

        if ($process->isSuccessful()) {
            return preg_replace('/cracklib-check: ([^ ]*) .*/', '$1', trim($process->getOutput() ) );
        }

        throw new RuntimeException('Unable to find cracklib-check command, please install cracklib-check');
    }

    /**
     * Performs an obscure check with the given password
     *
     * @param string $password
     * @throws ProcessFailedException
     */
    public function check($password) {
        $process = new Process($this->command);
        $process->setInput($password);
        $process->mustRun();

        return $this->verifyOutput($process->getOutput() );
    }

    /**
     * Checks the result of the password check
     *
     * @param string $output
     */
    private function verifyOutput($output) {
        $output = trim($output);
        $result = 'unknown';
        if (preg_match('/: ([^:]+)$/', $output, $matches) ) {
            $result = $matches[1];
        }

        $this->message = $result;

        if (array_key_exists($result, $this->messageStatus) ) {
            $this->status = $this->messageStatus[$result];
        }
        else {
            $this->status = $this->messageStatus['unknown'];
        }

        return ($this->status === 0);
    }

    /**
     * Gives the last status result of @see check
     *
     * @return integer
     */
    public function getLastStatus() {
        return $this->status;
    }

    /**
     * Gives the last message result of @see check
     *
     * @return string
     */
    public function getLastMessage() {
        return $this->message;
    }
}