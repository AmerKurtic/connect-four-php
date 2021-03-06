<?php
/**
 * NOTICE OF LICENSE
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @copyright   Copyright (c) 2012
 * @license     http://opensource.org/licenses/MIT  The MIT License (MIT)
 */

/**
 * Connect Four
 *
 * @author     Low Yong Zhen <cephyz@gmail.com>
 */

/**
 * @TODO Currently no tracking and printing for number of pieces dropped into each col for each player.
 */

class ConnectFour {

    /**
     * Default rows is 6
     *
     * @var int
     */
    protected $_rows = 6;

    /**
     * Default columns is 6
     *
     * @var int
     */
    protected $_columns = 6;

    /**
     * The board array to store information about player's pieces
     *
     * @var array
     */
    protected $_board_array = array();

    /**
     * Player 1 = 1, Player 2 = 2, No Player Selected = 0
     *
     * @var int
     */

    protected $_current_player = 0;

    /**
     * Track moves executed by both players.
     *
     * @var int
     */
    protected $_moves = 0;



    /**
     * CONSTRUCTOR
     * Starts the game on new instance
     * @param int $rows
     * @param int $cols
     */
    function __construct( $rows = 6, $cols = 6){
        session_start();
        if(isset($_SESSION['_board_array']) && $_SESSION['_board_array'] != "" && isset($_POST['collomnr']))
        {
            if (!empty($_SESSION['_moves'])){

                $this->_moves = $_SESSION['_moves'];
            }
            $this->_board_array = $_SESSION['_board_array'];

        }


        $this->_setDimensions( $rows, $cols );

        $this->_initGame();

        if (!isset($_POST['collomnr'])){
            $this->_printBoard();
        }
    }

    /**
     * Creates or resets a 2d board array
     *
     * @desc This is a better upgrade for initializeGameBoard method as described in the assignment.
     * Please note this method will not include a parameter since it creates the 2d array. (contrary to requirements)
     * This method effectively creates/resets the gameboard, assigning values while creating, looping only once.
     *
     * Alternatively, the assignment assumes you will use a static 6x6 board, in that case, which you can create a static 2d array and pass it to this function.
     */
    protected function _initializeGameBoard(){

        //resets the board array

        $_board_array = array();

        for($i = 0; $i < $this->getRows() ; $i ++ ){

            $_board_array[$i] = array();

            for($j = 0; $j < $this->getColumns() ; $j ++ ){

                //-1 means this slot is unoccupied.
                $_board_array[$i][$j] = -1;

            }

        }

        $this->_setCurrentBoard($_board_array);

    }

    /**
     * The game board is initialized here and first move will begin after starting player is set.
     */
    protected function _initGame()
    {

        //Setup our game board
        if(count($this->_board_array) == 0) {
            $this->_initializeGameBoard();
        }

        //Set a random player to start first


        if (isset($_POST['collomnr'])){
            //start dropping pieces
            $this->_dropPiece($_POST['collomnr'], 1);



            $this->_dropPiece($this->_getComputerTurn(), 2);
        }


    }

    /**
     * Creates a 'move' for each player by randomly choosing a column to drop a piece into.
     */
    protected function _dropPiece($_collomnr, $_current_player){

//test
        //Check if total moves reached. (Recursive baseline)
        if( $this->_moves >= ( $this->getRows() * $this->getColumns() )) {

            //No winner then =(
            $this->_showNoWinnerMessage();
            session_destroy();
            return false;
        }


        //Random column chosen for placing chips

            $_target_col = $_collomnr;

            $_current_board = $this->_getCurrentBoard();

        for( $row = $this->getRows()-1; $row>=0; $row-- ){
            //If slot is currently empty
            if( $_current_board[$row][$_target_col] === -1 ){

                //Set slot to current player
                $_current_board[$row][$_target_col] = $_current_player;

                //Update the no. of moves, might wana setter/getter this
                $this->_moves++;
                $_SESSION['_moves'] = $this->_moves;

                //Update the board
                $this->_setCurrentBoard($_current_board);

                //Print current board
                $this->_printBoard();

                //Check for winner
                if( $this->_checkForWinner( $row, $_target_col ) ){

                    //If winner is found
                    $this->_showWinnerMessage();
                    session_destroy();
                    return false;

                }
                $this->_togglePlayer();
                //exit once a piece is dropped for this move
                return false;

            }

        }

        //If it comes to here, it means no slots are empty (column is full). Redo move again
        $this->_dropPiece($_collomnr, $_current_player);

    }

    /**
     * Print out each step (board and details)
     */
    protected function _printBoard(){

        print '<p>Player '. $this->_getCurrentPlayer() .': Move No. ' . $this->_moves . '</p>';
        //print '<table>';
        print        '<tr>

    <form action="index.php" method="post">';
        print        '<td><input type="submit" name="collomnr" value="0" ' . $this->isColumnFull(0) . '></td>';
        print        '<td><input type="submit" name="collomnr" value="1" ' . $this->isColumnFull(1) . '></td>';
        print        '<td><input type="submit" name="collomnr" value="2" ' . $this->isColumnFull(2) . '></td>';
        print        '<td><input type="submit" name="collomnr" value="3" ' . $this->isColumnFull(3) . '></td>';
        print        '<td><input type="submit" name="collomnr" value="4" ' . $this->isColumnFull(4) . '></td>';
        print        '<td><input type="submit" name="collomnr" value="5" ' . $this->isColumnFull(5) . '></td>';
        print        '</form></tr>';
       // print '</table>';

        print '<table>';


        $_board_array = $this->_getCurrentBoard();

        for($i = 0; $i < $this->getRows() ; $i ++ ){
            print '<tr>';

            for($j = 0; $j < $this->getColumns() ; $j ++ ){

                //decoration
                $_class = "";

                if( $_board_array[$i][$j] === 1 ){
                    //player 1 color
                    $_class = "player-1";

                }else if( $_board_array[$i][$j] === 2 ){
                    //player 2 color
                    $_class = "player-2";

                }

                print '<td class="'.$_class.'" >' . $_board_array[$i][$j] . '</td>';

            }


            print '</tr>';
        }




        print '</table>';
    }

    /**
     * Displays the message for the winner
     */
    protected function _showWinnerMessage(){

        print '<p class="message">Player ' . $this->_getCurrentPlayer() .' wins the game!</p>';

    }

    /**
     * Displays the message if there's no winner
     */
    protected function _showNoWinnerMessage(){

        print '<p class="message">No winner for this round.</p>';

    }

    /**
     * Switches the turn to the other player
     */
    protected function _togglePlayer(){

        $this->_setCurrentPlayer($this->_getCurrentPlayer()===1?2:1);
    }

    /**
     * Gets the player for the current turn
     *
     * @return int
     */
    protected function _getCurrentPlayer(){

        return $this->_current_player;

    }

    /**
     * Sets the player for the current turn
     */
    protected function _setCurrentPlayer( $player_no ){

        $this->_current_player = $player_no;
        $_SESSION['_current_player'] = $this->_current_player;
    }

    /**
     * Gets the current board array
     *
     * @return array
     */
    protected function _getCurrentBoard(){

        return $this->_board_array;

    }

    /**
     * Sets the current board array
     */
    protected function _setCurrentBoard( $board_array ){
$this->_board_array = $board_array;
        $_SESSION['_board_array'] = $board_array;

    }


    /**
     * Check for winner
     *
     * @return boolean
     */
    protected function _checkForWinner( $row, $col ){

        if($this->_horizontalCheck($row, $col)
            || $this->_verticalCheck($row, $col)
        ){
            return true;
        }

        return false;

    }

    /**
     * Check for horizontal pieces
     *
     * @return boolean
     */
    private function _horizontalCheck( $row, $col ){

        $_board_array = $this->_getCurrentBoard();
        $_player = $_board_array[$row][$col];
        $_count = 0;

        //count towards the left of current piece
        for ( $i = $col; $i>=0; $i-- )
        {

            if( $_board_array[$row][$i] !== $_player ){

                break;

            }

            $_count++;

        }

        //count towards the right of current piece
        for ( $i = $col + 1; $i<$this->getColumns(); $i++ )
        {

            if( $_board_array[$row][$i] !== $_player ){

                break;

            }

            $_count++;

        }

        return $_count>=4 ? true : false;

    }

    /**
     * Check for vertical pieces
     *
     * @return boolean
     */
    private function _verticalCheck( $row, $col ){

        //if current piece is less than 4 pieces from bottom, skip check
        if ( $row >= $this->getRows()-3 ) {

            return false;

        }

        $_board_array = $this->_getCurrentBoard();
        $_player = $_board_array[$row][$col];

        for ( $i = $row + 1; $i <= $row + 3; $i++ ){

            if($_board_array[$i][$col] !== $_player){

                return false;

            }

        }

        return true;

    }
     public function isColumnFull($colIndex) {
         $_board_array = $this->_getCurrentBoard();
         $_rows = $this->getRows();

         $isFull = "";
         //echo "<pre>";
         //var_dump($this->_board_array);
         //die;
         if(-1 != $_board_array[0][$colIndex])
         {
             $isFull = "disabled";
         }

         return $isFull;
     }

    protected function _getComputerTurn(){
        $_collomnr = rand(0, 5);
        if ($this->_horizontalPossibleWinCheck() !== false){
            $_collomnr = $this->_horizontalPossibleWinCheck();
        } elseif ($this->_verticalPossibleWinCheck() !== false){
            $_collomnr = $this->_verticalPossibleWinCheck();
        }
        return $_collomnr;
    }

    private function _horizontalPossibleWinCheck(){
        $_board_array = $this->_getCurrentBoard();
        $empty = "";
        $count = 0;
        for($r=1; $r<$this->_rows;$r++) {
            for ($c = 0; $c < ($this->_columns - 3); $c++) {
                for ($i = $c; $i < ($c+3); $i++) {
                    if ($_board_array[$r][$i] == 1) {

                        $count++;

                    }
                    elseif($_board_array[$r][$i] == "-1")
                    {
                        $empty = $c;
                    }
                }
                if($count == 3 && !empty($empty))
                {
                    $d = $r + 1;
                    if($_board_array[$d][$i] != "-1")
                    {
                        return $empty;
                    }
                }
            }
        }
        return false;
    }

    private function _verticalPossibleWinCheck(){

        $_board_array = $this->_getCurrentBoard();
        $empty = "";
        for($c=1; $c<$this->_columns;$c++) {
            for ($r = ($this->_rows - 3); $r > 0; $r--) {
                $count = 0;
                $first = true;
                for ($i = $r; $i < ($r+3); $i++) {
                    if($first)
                    {
                        if($_board_array[$i][$c] == "-1")
                        {
                            $empty = $c;
                        }
                        $first = false;
                    }
                    if ($_board_array[$i][$c] == 1) {

                        $count++;
                    }
                }
                if($count == 3 && !empty($empty))
                {
                        return $empty;
                }
            }
        }
        return false;
    }

    /**
     * Set the number of rows and columns for the board
     *
     * @param int $rows
     * @param int $cols
     */
    protected function _setDimensions($rows = 6, $cols = 6){

        if(!isset($rows)) return;

        $this->setRows($rows);
        $this->setColumns($cols===null?$rows:$cols);

    }

    /**
     * Set the number of rows for the board
     *
     * @param int $rows
     */
    public function setRows($rows = 6){

        $this->_rows = $rows;

    }

    /**
     * Get the number of rows for the board
     *
     * @return int
     */
    public function getRows(){

        return $this->_rows;

    }

    /**
     * Set the number of columns for the board
     *
     * @param int $col
     */
    public function setColumns($col = 6){

        $this->_columns = $col;

    }

    /**
     * Get the number of columns for the board
     *
     * @return int
     */
    public function getColumns(){

        return $this->_columns;

    }

}//end: ConnectFour
?>