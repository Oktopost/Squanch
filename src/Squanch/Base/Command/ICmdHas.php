<?php
namespace Squanch\Base\Command;


interface ICmdHas extends ISetupWithConnectorAndCallbacksLoader, ICommand, IWhere, IResetTTL
{
}