<?php
namespace Squanch\Base\Command;


interface ICmdHas extends IConstructWithConnectorAndCallbacksLoader, ICommand, IWhere, IResetTTL
{
}