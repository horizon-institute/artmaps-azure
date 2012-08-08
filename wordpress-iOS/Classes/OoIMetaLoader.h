//
//  OoIMetaLoader.h
//  WordPress
//
//  Created by Shakir Ali on 21/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import "DataLoader.h"
#import "OoIMeta.h"

@protocol OoIMetaLoaderDelegate;

@interface OoIMetaLoader : DataLoader{
    id<OoIMetaLoaderDelegate> delegate;
    NSIndexPath *indexPathInTableView;
}
-(void)submitOoIMetaRequestWithID:(NSNumber*)metaID;
-(void)submitOoIMetaRequestWithID:(NSNumber*)metaID forIndexPathInTableView:(NSIndexPath*)indexPath;

@property (assign) id<OoIMetaLoaderDelegate> delegate;
@property (nonatomic, retain) NSNumber* refObjID;
@property (nonatomic, retain) NSIndexPath *indexPathInTableView;

@end

@protocol OoIMetaLoaderDelegate<NSObject>
@optional
-(void)OoIMetaLoader:(OoIMetaLoader*)loader didFailWithError:(NSError*)error;
-(void)OoIMetaLoader:(OoIMetaLoader *)loader didLoadOoIMeta:(OoIMeta*)ooiMeta;
-(void)metaDataDidLoad:(OoIMeta*)ooiMeta forIndexPath:(NSIndexPath*)indexPath;
@end