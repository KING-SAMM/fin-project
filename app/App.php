<?php

    declare(strict_types=1);

    # Get files from directories
    function getTransactionFiles(string $dirPath): array
    {
        $files = [];

        foreach(scandir($dirPath) as $file)
        {
            if (is_dir($file))
            {
                continue;
            }

            $files[] = $dirPath . $file;
        }

        return $files;
    }

    # Read files and extract transactions from them
    function getTransactions(string $fileName, ?callable $transactionHandler = null): array
    {
        if(! file_exists($fileName))
        {
            trigger_error('File "'. $fileName .'" does not exist.', E_USER_ERROR);
        }

        # Open the file for reading
        $file = fopen($fileName, 'r');

        # Read off first line which is not an actual transaction
        fgetcsv($file);

        $transactions = [];

        while(($transaction = fgetcsv($file)) !== false)
        {
            # Pass transaction through transactionHandler if present
            if ($transactionHandler !== null)
            {
                $transaction = $transactionHandler($transaction);
            }
            $transactions[] = $transaction;
        }

        return $transactions;
    }

    # Handler to format each trabsaction row
    function extractTransaction(array $transactionRow): array
    {
        # Destructure parameters from $transactionRow
        [$date, $chequeNumber, $description, $amount] = $transactionRow;
        # Remove money signs and commas from $amount and cast to float
        $amount = (float) str_replace(['NGN', '$', ','], '', $amount);

        return [
            'date' => $date,
            'chequeNumber' => $chequeNumber,
            'description' => $description,
            'amount' => $amount
        ];
    }


    # Calculate the totals
    function calculateTotals(array $transactions): array
    {
        $totals = ['netTotal' => 0, 'totalIncome' => 0, 'totalExpense' => 0];

        foreach($transactions as $transaction)
        {
            $totals['netTotal'] += $transaction['amount'];

            if($transaction['amount'] >= 0)
            {
                $totals['totalIncome'] += $transaction['amount'];
            }
            else
            {
                $totals['totalExpense'] += $transaction['amount'];
            }
        }

        return $totals;
    }