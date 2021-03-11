<?php

namespace App\Http\Controllers;

use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function buyTicket(Request $request, Ticket  $ticket)
    {   
        //開始交易
        DB::beginTransaction();
        try {
            //查詢時進行鎖表
            $ticket = Ticket::lockForUpdate()->find($ticket->id);
        
            // 檢查票券是否還有足夠數量
            if ($ticket->available_amount <= 0) {
                throw new TicketUnavailableException('Ticket amount is not available');
            }
            
            // 中間購買邏輯省略
            
            // 扣除票券剩餘數量
            $ticket->update([
                'available_amount' => $ticket->available_amount - 1,
            ]);

           
        } catch (\Exception $e) {
            DB::rollback();
            return [
                'success' => false,
                $e->getMessage()
            ];
        }

        DB::commit();

        return [
            'success' => true
        ];
  
    }
}
